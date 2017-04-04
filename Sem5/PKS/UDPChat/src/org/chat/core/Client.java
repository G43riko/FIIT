package org.chat.core;

import java.io.IOException;
import java.net.DatagramPacket;
import java.net.DatagramSocket;
import java.net.InetAddress;
import java.net.SocketException;

import org.chat.Config;
import org.chat.UDPChat;
import org.chat.utils.Log;

public final class Client extends Connection{
	
	public Client(UDPChat parent) {
		super(parent);
		Log.write("zaèal konštruktor objektu Client", Log.CONSTRUCTORS);
		lastContact = System.currentTimeMillis();
		try {
			socket = new DatagramSocket(parent.getPort() + 1);
			listen();
		} catch (SocketException e) {
			Log.write("Nepodarilo sa vytvori� client socket", e, Log.EXCEPTIONS);
		}
		Log.write("skonèil konštruktor objektu Client", Log.CONSTRUCTORS);
	}

	private void listen(){
		listen = new Thread(new Runnable(){
			@Override
			public void run() {
				while(isRunning()){
					try {
						byte[] block = new byte[Config.CHAT_TOTAL_MAX_MSG_SIZE];
						DatagramPacket inpacket = new DatagramPacket(block, block.length);
						socket.receive(inpacket);
						proccessMessage(new String(inpacket.getData(), 0, inpacket.getLength()));
					} catch (IOException e) {
						Log.write("Client socket bol zatvorený",e,  Log.EXCEPTIONS);
					}
				}
				socket.close();
			}
		});
		
		listen.start();
	}

	@Override
	public void write(String message){
		if(!isRunning())
			return;
		try {
			DatagramPacket outpacket = getPacket(message, 
												 InetAddress.getByName(getParent().getIp()), 
												 getParent().getPort()) ;
			
			socket.send(outpacket);
			Log.write("client odoslal správu: " + message, Log.CONNECTION);
		} catch (IOException e) {
			Log.write("s klienta sa epodarilo odosla� správu: " + message, e, Log.EXCEPTIONS);
		}
	}
	
	@Override
	public boolean isServer() {return false;}
}
