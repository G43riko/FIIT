package org.chat.utils;

import java.lang.reflect.Array;
import java.net.InetAddress;
import java.net.UnknownHostException;
import java.nio.ByteBuffer;

public class Utils {
	public static byte[] getByteArray(int value) {
	     return  ByteBuffer.allocate(4).putInt(value).array();
	}
	
	public static int getInt(byte[] bytes) {
	     return ByteBuffer.wrap(bytes).getInt();
	}
	
	public static char[] convertByteArrayCharArray(byte[] in){
		char[] out = new char[in.length];
		for(int i=0 ; i<in.length ; i++)
			out[i] = (char)in[i];
		return out;
	}
	
	public static byte[] concatenate (byte[] a, byte b[]) {
	    int aLen = a.length;
	    int bLen = b.length;

	    byte[] c =(byte[]) Array.newInstance(a.getClass().getComponentType(), aLen+bLen);
	    System.arraycopy(a, 0, c, 0, aLen);
	    System.arraycopy(b, 0, c, aLen, bLen);

	    return c;
	}
	
	public static String getBytesOfInt(int number){
		return String.format("%8s", Integer.toBinaryString(number & 0xFF)).replace(' ', '0');
	}
	
	public static void drawByteArray(byte[] array){
		for(byte b : array)
			System.out.print(b + ", ");
		
		System.out.println("");
	}
	
	public static<T> void drawArray(T[] array){
		for(T b : array)
			System.out.print(b + ", ");
		
		System.out.println("");
	}
	
	public static String getBitsFromByteArray(byte[] array){
		StringBuilder result = new StringBuilder();
		for(byte b : array)
			result.append(getBytesOfInt((int)b));
		
		return result.toString();
	}
	
	public static byte[] getBitsFromString(String s){
		byte[] sArray = s.getBytes();
		for(int i=0 ; i<s.length() ; i++)
			if(sArray[i] == 48)
				sArray[i] = 0;
			else if(sArray[i] == 49)
				sArray[i] = 1;
		
		return sArray;
	}
	
	public static void sleep(int ms){
		try {
				Thread.sleep(ms);
		} catch (InterruptedException e) {
			Log.write("nepodarilo sa uspa vlákno", e, Log.EXCEPTIONS);
		}
	}

	public static String getMyIP() {
		try {
			return InetAddress.getLocalHost().getHostAddress();
		} catch (UnknownHostException e) {
			Log.write("nepodarilo sa získa adresu localhostu", e, Log.EXCEPTIONS);
		}
		return "Error";
	}
	
//	public static<T> T[] concatenate (T[] a, T[] b) {
//	    int aLen = a.length;
//	    int bLen = b.length;
//
//	    @SuppressWarnings("unchecked")
//	    T[] c = (T[]) Array.newInstance(a.getClass().getComponentType(), aLen+bLen);
//	    System.arraycopy(a, 0, c, 0, aLen);
//	    System.arraycopy(b, 0, c, aLen, bLen);
//
//	    return c;
//	}
}
