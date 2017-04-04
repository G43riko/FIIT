package org.chat.core;

import java.net.DatagramSocket;

import org.chat.UDPChat;

public abstract class Connection implements Connectionable{
	private UDPChat 			parent;
	private boolean 			running 	= true;
	protected long 				lastContact;
	protected DatagramSocket 	socket;
	protected Thread 			listen;
	
	public Connection(UDPChat parent){
		this.parent = parent;
	}
	
	protected void proccessMessage(String message){
		if(!running)
			return;
//		Log.write("server prijal správu " + message, Log.CONNECTION);
		
		
		
		parent.getMessageManager().proccessAllRecievedMessages(message);
	}

	public void stop() {
//		listen.interrupt();
		running = false;
	}

	@Override
	public void setLastContact(long lastContact) {this.lastContact = lastContact;}
	public long getLastContact() {return lastContact;}
	protected boolean isRunning() {return running;}
	protected UDPChat getParent() {return parent;}
}
