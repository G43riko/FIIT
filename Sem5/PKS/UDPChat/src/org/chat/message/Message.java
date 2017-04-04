package org.chat.message;

import java.io.File;
import java.io.IOException;
import java.nio.file.Files;
import java.util.ArrayList;
import java.util.Base64;
import java.util.HashMap;

import org.chat.Config;
import org.chat.utils.IDGenerator;
import org.chat.utils.Log;
import org.chat.utils.StringXORer;
import org.chat.utils.Utils;

public class Message {
	private HashMap<Integer, MessagePart> messages = new HashMap<Integer, MessagePart>();
	private MessageManager parent;
	private int id;
	private int parts;
	private String fileName;
	private boolean okey = false;
	private long lastContact;
	private int maxFileOrder = 0;
	private boolean bad = false;
	
	//CONSTRUCTORS
	
	public Message(MessageManager parent, MessagePart msg){
		Log.write("zaèal konštruktor objektu Message", Log.CONSTRUCTORS);
		this.parent = parent;
		parts = msg.getNumber();
		id = msg.getId();
		
		
		recievePart(msg);
		Log.write("skonèil konštruktor objektu Message", Log.CONSTRUCTORS);
	}

	
	public Message(String message, MessageManager parent, int id, byte messageType){
		this.parent = parent;
		this.id = id;
			

		if(message.contains(Config.CHAT_BAD_MSG)){
			this.bad = true;
			message = message.replace(Config.CHAT_BAD_MSG, "");
		}
		
		ArrayList<String> msgs = divideMessage(message, parent.getParent().getMaxMsgLenght());
		parts = msgs.size();
		for(int i=0 ; i<parts ; i++){
			MessagePart msg = new MessagePart(msgs.get(i), null, id, msgs.size(), i, messageType);
			messages.put(i, msg);
		}
		encodeAndSend(messages.get(0));
	}
	
	public Message(File file, MessageManager parent, int id) {
		this.parent = parent;
		this.id = id;
		
		Log.write("Message.Message(File,MessageManager,int): id je:" + id, Log.DEBUG);
		try {
			String data = Base64.getEncoder().encodeToString(Files.readAllBytes(file.toPath()));
			ArrayList<String> msgs = divideMessage(data, parent.getParent().getMaxMsgLenght());
			parts = msgs.size();
			for(int i=0 ; i<parts ; i++){
				MessagePart msg = new MessagePart(msgs.get(i),
												  i == 0 ? file.getName() : null, 
												  id, 
												  msgs.size(), 
												  i, 
												  MessageManager.MESSAGE_FILE);
				messages.put(i, msg);
			}
			encodeAndSend(messages.get(0));
			
		} catch (IOException e) {
			Log.write("nepodarilo sa vytvoriš správu pre odoslanie súboru", e, Log.EXCEPTIONS);
		} 
	}

	private void encodeAndSend(MessagePart part){
		part.increaseSendTimes();
		
		if(part.getSendTimes() >= Config.CHAT_MAX_SEND_NUMBER)
			return;
		
		int msgID = IDGenerator.getId();
		String finalMessage = StringXORer.encode(new String(part.getData()), msgID);
		if(bad){
			finalMessage += new String(Utils.getByteArray(msgID / 2));
			bad = false;
		}
		else
			finalMessage += new String(Utils.getByteArray(msgID));
		parent.getParent().getConnection().write(finalMessage);
	};
	
	private void messageProccess() {
		switch(messages.get(0).getType()){
			case MessageManager.MESSAGE_TEXT :
				parent.getParent().recieveMessage(getText());				
				break;
			case MessageManager.MESSAGE_WELCOME :
				parent.proccessWelcomeMessage(getText());
				break;
			case MessageManager.MESSAGE_FILE :
				parent.proccessFileMessage(getText(), fileName);
				break;
			case MessageManager.MESSAGE_LOGOUT :
				parent.proccessLogoutMessage();
				break;
			case MessageManager.MESSAGE_PING :
				parent.proccessPingMessage();
				break;
			case MessageManager.MESSAGE_REPAIR :
				parent.proccessRepairMessage(this);
				break;
			case MessageManager.MESSAGE_FINISH :
				parent.proccessFinishMessage(getText());
				break;
		}
	}
	
	private ArrayList<String> divideMessage(String message, int maxLength){
		ArrayList<String> result = new ArrayList<String>();
		while(message.length() > maxLength){
			result.add(message.substring(0, maxLength));
			message = message.substring(maxLength, message.length());
		}
		result.add(message);
		return result;
	}
	
	public void recievePart(MessagePart msg) {
		maxFileOrder++;
		
		if(maxFileOrder + 1 != msg.getOrder()){
			msg.setOrder(maxFileOrder - 1);
		}
		
		if(!messages.containsKey(msg.getOrder()) || !messages.get(msg.getOrder()).isOkey() || msg.getType() == MessageManager.MESSAGE_REPAIR)
			if(msg.getType() != MessageManager.MESSAGE_FINISH)
				parent.createFinishedMessage(msg);
		
		if(messages.containsKey(msg.getOrder()))
			return;
					
		messages.put(msg.getOrder(), msg);
		
		if(msg.getType() == MessageManager.MESSAGE_FILE && msg.getOrder() == 0)
			this.fileName = msg.getFileName();
		
		if(isComplete())
			messageProccess();
	}

	public String getText() {
		String res = messages.get(0).getText();
		for(int i=1 ; i<parts ; i++)
			res += messages.get(i).getText();
		return res;
	}
	
	private boolean isComplete() {
		return messages.size() == parts;
	}
	public int getId() {return id;}

	public void resend(int order) {
		encodeAndSend(messages.get(order));
	}
	@Override
	public String toString() {
		return "id: " + id +", parts: " + parts;
	}

	public boolean hasMessage(MessagePart msg) {
		return messages.containsKey(msg.getOrder());
	}

	public int getSize() {
		return messages.size();
	}

	public void wasSendSuccesfully(Integer num) {
		if(messages.containsKey(num))
			messages.get(num).setOkey();
		
		lastContact = System.currentTimeMillis();
		maxFileOrder++;
		
		if(num+1 != maxFileOrder && messages.containsKey(maxFileOrder - 1))
			encodeAndSend(messages.get(maxFileOrder - 1));
		
		Utils.sleep(100);
		
		
		if(messages.containsKey(num + 1)){
			Log.write("a odosiela sa dalšia časť " + (num + 1) + " == " + maxFileOrder, Log.DEBUG);
			encodeAndSend(messages.get(maxFileOrder));
		}
	}

	public boolean isOkey() {
		return okey;
	}

	public long getLastContact() {
		return lastContact;
	}

	public void resend() {
		if(messages.get(0).getType() != MessageManager.MESSAGE_FILE && messages.get(0).getType() != MessageManager.MESSAGE_TEXT)
			return;

		Log.write("správa bola s dovodu neaktivy vyžiadaná znovu", Log.DEBUG);
		lastContact = System.currentTimeMillis();
		
		int i = 0;
		while(messages.get(i++).isOkey());
		encodeAndSend(messages.get(i - 1));
		
	}
}
