package org.chat.utils;

public class LogMessage {
	private long 		created = System.nanoTime();
	private String 		text;
	private Exception 	error;
	
	public LogMessage(String text, Exception error) {
		this.text = text;
		this.error = error;
	}

	public long getCreated() {return created;}
	public String getText() {return text;}
	public Exception getError() {return error;}
}
