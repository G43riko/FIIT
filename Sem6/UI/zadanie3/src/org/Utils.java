package org;

import java.nio.ByteBuffer;

public class Utils{
	public static String getBytesOfInt(int number){
		return String.format("%8s", Integer.toBinaryString(number & 0xFF)).replace(' ', '0');
	}
	
	public static int getIntValue(byte[] num){
		int result = 0;
		for(int i=0 ; i<num.length ; i++)
			if(num[i] == '1')
				result += Math.pow(2, num.length - i - 1);
		
		return result;
	}
	
	public static byte[] getByteArray(int value) {
	     return  ByteBuffer.allocate(4).putInt(value).array();
	}
	
	public static byte[] getByteArray2(char value) {
	     return  ByteBuffer.allocate(4).putChar(value).array();
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
	
	public static int getInt(byte[] bytes) {
	     return ByteBuffer.wrap(bytes).getInt();
	}
	
	public static int getInt2(byte[] bytes) {
	     return (int)ByteBuffer.wrap(bytes).getChar();
	}
	
	public static int getIntFromString(byte[] bytes) {
		for(int i=0 ; i< bytes.length ; i++)
			bytes[i] -= '0'; 
	     return ByteBuffer.wrap(bytes).getInt();
	}
}
