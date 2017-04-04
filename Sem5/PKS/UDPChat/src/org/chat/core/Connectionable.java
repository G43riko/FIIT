package org.chat.core;

import java.net.DatagramPacket;
import java.net.InetAddress;

public interface Connectionable {
	public void stop();
	public void write(String message);
	public void setLastContact(long time);
	public boolean isServer();
	
	public default DatagramPacket getPacket(String message, InetAddress address, int port){
		return new DatagramPacket(message.getBytes(), 
								  message.length(), 
								  address, 
								  port);
	}
	public long getLastContact();
}

