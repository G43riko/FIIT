package org.chat.message;

import java.io.BufferedOutputStream;
import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.util.Arrays;
import java.util.Base64;
import java.util.HashMap;

import javax.swing.JFileChooser;
import javax.swing.JOptionPane;

import org.chat.UDPChat;
import org.chat.core.Server;
import org.chat.utils.IDGenerator;
import org.chat.utils.Log;
import org.chat.utils.StringXORer;
import org.chat.utils.Utils;

public class MessageManager {
	public final static byte MESSAGE_TEXT 		= 0;
	public final static byte MESSAGE_PING 		= 1;
	public final static byte MESSAGE_FILE 		= 3;
	public final static byte MESSAGE_LOGOUT 	= 4;
	public final static byte MESSAGE_WELCOME	= 5;
	public final static byte MESSAGE_REPAIR		= 6;
	public final static byte MESSAGE_FINISH		= 7;
	
	private HashMap<Integer, Message> messages = new HashMap<Integer, Message>();
	private UDPChat parent;

	//CONTRUCTORS
	
	public MessageManager(UDPChat parent) {
		Log.write("zaèal konštruktor objektu MessageManager", Log.CONSTRUCTORS);
		this.parent = parent;
		Log.write("skonèil konštruktor objektu MessageManager", Log.CONSTRUCTORS);
	}
	
	//OTHERS
	
	private MessagePart parseHeader(byte[] data){
		int order = Utils.getInt(Arrays.copyOfRange(data, 8, 12));
		if(data[12] == MESSAGE_FILE && order == 0){
			int fileNameLengh = Utils.getInt(Arrays.copyOfRange(data, 13, 17));
			return new MessagePart(new String(data, 12, data.length - 12),
					   			   new String(Arrays.copyOfRange(data, 17, 17 + fileNameLengh)),
					   			   Utils.getInt(Arrays.copyOfRange(data, 0, 4)), 
					   			   Utils.getInt(Arrays.copyOfRange(data, 4, 8)), 
					   			   order,
					   			   data[12]);
		}
		else
			return new MessagePart(new String(data, 12, data.length - 12),
								   null,
								   Utils.getInt(Arrays.copyOfRange(data, 0, 4)), 
								   Utils.getInt(Arrays.copyOfRange(data, 4, 8)), 
								   order, 
								   data[12]);
	}
	
	//CREATORS
	
	public void createRepairMessage(int order, int msgID, byte type) {
		int id = IDGenerator.getId();
		messages.put(id, new Message(msgID + ":" + order + ":" + type, this, id, MESSAGE_REPAIR));
	}
	
	public void createTextMessage(String message) {
		int id = IDGenerator.getId();
		messages.put(id, new Message(message, this, id, MESSAGE_TEXT));
	}
	
	public void createFileMessage(File file) {
		int id = IDGenerator.getId();
		Log.write("MessageManager.createFileMessage: id: " + id, Log.DEBUG);
		messages.put(id, new Message(file, this, id));
	}

	public void createFinishedMessage(MessagePart msg) {
		int id = IDGenerator.getId();
		Log.write("odosiela sa správa o úspešnom doručení: " + msg.getId() + ":" + msg.getOrder() + ":" + msg.getNumber() + ":" + msg.getType(), Log.FIXER);
		messages.put(id, new Message(msg.getId() + ":" + msg.getOrder() + ":" + msg.getNumber() + ":" + msg.getType(), this, id, MESSAGE_FINISH));
//		TODO messages.remove(msgID);
	}
	
	public void createWelcomeMessage(){
		int id = IDGenerator.getId();
		messages.put(id, new Message(parent.getLogin() + ":" + Utils.getMyIP(), this,  id,  MESSAGE_WELCOME));
	}
	
	public void createLogoutMessage(){
		int id = IDGenerator.getId();
		messages.put(id, new Message("", this,  id,  MESSAGE_LOGOUT));
	}

	public void createPingMessage() {
		int id = IDGenerator.getId();
		messages.put(id, new Message("", this,  id,  MESSAGE_PING));
		Log.write("odosiela sa pingovacia správa", Log.PING_MESSAGE);
	}
	
	//PROCCESSORS
	
	public void proccessLogoutMessage() {
		//TODO ak to bude blbnuť toto zakomentovať
		createLogoutMessage();
		if(parent.isServer())
			parent.recieveMessage("uživatel " + parent.getOponentName() + " sa odpojil");
		else
			parent.recieveMessage("server uživate " + parent.getOponentName() + " bol zrušený");
		parent.setOponenName(null);
	}
	
	public void proccessWelcomeMessage(String message) {
		String[] text = message.split(":");
		parent.setOponenName(text[0]);
		parent.setIp(text[1]);
		if(parent.isServer()){
			createWelcomeMessage();
			((Server)parent.getConnection()).startMessageChecking();
			parent.recieveMessage("pripojil sa uživaťel " + text[0]);
		}
	}
	
	public void proccessFileMessage(String text, String fileName){
		text = text.replace(fileName, "").trim();
		parent.recieveMessage("prijali ste súbor: " + fileName);
		try {
			String message = "bol priajatý súbor " + fileName + ", želáte si ho uloži�??";
			JFileChooser fc = new JFileChooser();
			if(JOptionPane.showConfirmDialog(parent.getGui(), message) == JOptionPane.YES_OPTION){
				fc.showSaveDialog(parent.getGui());
				
				BufferedOutputStream o = new BufferedOutputStream(new FileOutputStream(fc.getSelectedFile()));
				o.write(Base64.getDecoder().decode(text));
				o.close();
			}

		} catch (IOException e) {
			Log.write("nepodarilo sa spracova� prijatý súbor", e, Log.EXCEPTIONS);
		}
	}

	public void proccessAllRecievedMessages(String message) {
		int crc = Utils.getInt(message.substring(message.length() - 4, message.length()).getBytes());
		message = message.substring(0, message.length() - 4);
		String finalMSG = StringXORer.decode(message, crc);
		
		if(finalMSG == null){
			int order = Utils.getInt(Arrays.copyOfRange(message.getBytes(), 8, 12));
			int id = Utils.getInt(Arrays.copyOfRange(message.getBytes(), 0, 4));
			
			if(message.getBytes()[12] != MESSAGE_REPAIR)
			Log.write("chyba v správe s order: " + order + ", id: " + id, Log.CONNECTION);
				createRepairMessage(order, id, message.getBytes()[12]);
			return;
		}
		
		MessagePart msg = parseHeader(finalMSG.getBytes());
		
		if(messages.containsKey(msg.getId()))
			messages.get(msg.getId()).recievePart(msg);
		else
			messages.put(msg.getId(), new Message(this, msg));
		
	}

	public void proccessRepairMessage(Message message){
		String[] content = message.getText().split(":");
		Log.write("znova sa odosiela správa: " + message, Log.CONNECTION);
		
		try{
			messages.get(Integer.parseInt(content[0])).resend(Integer.parseInt(content[1]));
		}
		catch(NullPointerException | NumberFormatException e){
			createRepairMessage(0, message.getId(), MESSAGE_REPAIR);
		}
	}
	
	public void proccessPingMessage() {
		parent.getConnection().setLastContact(System.currentTimeMillis());
		Log.write("bola prijatá pingovacia správa", Log.PING_MESSAGE);
		
		if(!parent.isServer())
			createPingMessage();
	}
	
	public void proccessFinishMessage(String message){
		String[] content = message.split(":");
		Log.write("bola prijatá správa o úspešnom doručení: " + message, Log.FIXER);
		if(messages.containsKey(Integer.valueOf(content[0])))
			messages.get(Integer.valueOf(content[0])).wasSendSuccesfully(Integer.valueOf(content[1]));
	}

	//GETTERS
	
	public UDPChat getParent() {return parent;}
}
