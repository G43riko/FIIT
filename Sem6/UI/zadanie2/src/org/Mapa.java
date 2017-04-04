package org;

import java.util.Collection;
import java.util.HashSet;
import java.util.Set;

public class Mapa{
	public static final GVector2f BLOCK_SIZE = new GVector2f(50, 50);
	Set<GVector2f> mapa = new HashSet<GVector2f>();
	
	public boolean isFree(GVector2f pos){
		return mapa.contains(pos);
	}
	
	public boolean isFree(Collection<GVector2f> pos){
		for(GVector2f p : pos)
			if(mapa.contains(p))
				return false;
		
		return true;
	}
	
	public void setFree(Collection<GVector2f> data){
		mapa.removeAll(data);
	}
	
	public void addData(Collection<GVector2f> data){
		mapa.addAll(data);
	}
	
	@Override
	public String toString() {
		return mapa.toString();
	}
}
