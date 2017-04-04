package org.chat.message;

import org.chat.utils.Utils;

public class MessagePart {
	private int 	id;
	private int 	number;
	private int 	order;
//	private int 	messageSize;
	private byte 	type;
	private byte[] 	data;
	private String 	text;
//	private String 	qrc;
	private String 	fileName;
	private int 	sendTimes = 1;
	private boolean okey = false;
//	private long 	sendTime = System.currentTimeMillis();
	//boolean send;
	
	//CONSTRUCTORS

	public MessagePart(String text, String fileName, int id, int number, int order, byte type) {
		this.number = number;
		this.order = order;
		this.type = type;
		this.fileName = fileName;
		this.text = text;
		this.id = id;
		
		
		data = createHeader(id, number, order, type, fileName);
		if(text != null){
			data = Utils.concatenate(data, text.getBytes());
		}
	}

	//id spravy, pocet sprav,cislo spravy, velkosù spr·vy, typ spravy, [velkosù nazvu suboru, nazov suboru]



	private  byte[] createHeader(int id, int messagesNumber, int messageOrder, byte type, String filename){
		//XIDXXMNXXMOXTXFLXfilenameDATA
		byte[]result = Utils.concatenate(Utils.getByteArray(id), Utils.getByteArray(messagesNumber));
			  result = Utils.concatenate(result, Utils.getByteArray(messageOrder));
			  result = Utils.concatenate(result, new byte[]{type});
		if(fileName != null){
			result = Utils.concatenate(result, Utils.getByteArray(filename.length()));
			result = Utils.concatenate(result, filename.getBytes());
		}
		
		return result;
	}
	
	public void increaseSendTimes(){
		sendTimes++;
	}
	
	//GETTERS

	public String 	getText() {if(text == null) return ""; return text.substring(1, text.length());}
	public boolean isOkey() {
		return okey;
	}

	public void setOkey() {
		this.okey = true;
	}

	public String 	getFileName() {return fileName;}
	public int 		getId() {return id;}
	public int 		getOrder() {return order;}
	public int 		getNumber() {return number;}
	public int 		getSendTimes() {return sendTimes;}
	public byte[] 	getData() {return data;}
	public byte 	getType() {return type;}

	public void setOrder(int order) {
		this.order = order;
	}


}

