package org.chat.utils;

import java.util.Arrays;

import org.chat.Config;

public class StringXORer {
	
	public static String decode(String word, int key){
		byte[] bytes = Utils.getBytesOfInt(key).getBytes();
		String res = word.substring(0, word.length() - Config.CHAT_CRC_LENGTH + 1);
		
		byte[] slovo = Utils.getBitsFromByteArray(res.getBytes()).getBytes();
		slovo = Utils.concatenate(slovo, word.substring(word.length() -Config.CHAT_CRC_LENGTH + 1,word.length()).getBytes());
		
		
		for(int i=0 ; i<bytes.length ; i++)
			bytes[i] -= 48;
		
		byte[] result = xorWithKey(slovo, bytes);
		
		
		byte[] postFix = new String(result).substring(result.length - Config.CHAT_CRC_LENGTH + 1, result.length).getBytes();
		
		if(!Arrays.equals(postFix, new byte[]{0,0,0,0,0,0,0}))
			return null;
		
		return res;
	}
	
	public static String encode(String word, int key) {
		byte[] slovo = Utils.concatenate(Utils.getBitsFromByteArray(new String(word).getBytes()).getBytes(), new byte[]{48,48,48,48,48,48,48});
		byte[] bytes = Utils.getBytesOfInt(key).getBytes();
		
		for(int i=0 ; i<slovo.length ; i++)
			slovo[i] -= 48;
		for(int i=0 ; i<bytes.length ; i++)
			bytes[i] -= 48;
		String res = new String(xorWithKey(slovo, bytes));
		res = res.substring(res.length() - Config.CHAT_CRC_LENGTH + 1, res.length());
		
		return word + res;
		
	}

    private static byte[] xorWithKey(byte[] a, byte[] key) {
        byte[] out = new byte[a.length];
        
        for (int i = 0; i < a.length; i++)
            out[i] = (byte) ((a[i] ^ key[i % key.length]));
       
        return out;
    }
}