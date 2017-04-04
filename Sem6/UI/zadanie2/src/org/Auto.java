package org;

import java.awt.Color;
import java.awt.Graphics2D;
import java.util.HashSet;
import java.util.Set;

public class Auto{
	public final static GVector2f VER = new GVector2f(0, 1);
	public final static GVector2f HOR = new GVector2f(1, 0);
	private final static int round = 20;
	private GVector2f direction;
	private GVector2f position;
	private Color color;
	private int size;
	private Set<GVector2f> datas = new HashSet<GVector2f>();
	
	public Auto(Color color, GVector2f position, int size, GVector2f direction, Mapa mapa) {
		this.direction = direction;
		this.position = position;
		this.color = color;
		this.size = size;
		datas.addAll(calcDatas());
		mapa.addData(datas);
	}
	
	private Set<GVector2f> calcDatas(){
		Set<GVector2f> data = new HashSet<GVector2f>();;
		for(int i=0 ; i<size ; i++)
			data.add(new GVector2f(position.add(direction.mul(i))));
		return data;
	}
	
	public boolean move(int move, Mapa map){
		GVector2f oldPos = position;
		position = position.add(direction.mul(move));
		map.setFree(datas);
		
		datas.clear();
		Set<GVector2f> d = calcDatas();
		
		if(!map.isFree(d)){
			position = oldPos;
			return false;
		}
		
		datas.addAll(d);
		map.addData(datas);
		
		return true;
	}
	
	public void draw(Graphics2D g2){
		g2.setColor(color);
		GVector2f pos = position.mul(Mapa.BLOCK_SIZE);
		GVector2f s = direction.mul(size - 1).mul(Mapa.BLOCK_SIZE).add(Mapa.BLOCK_SIZE);
		
		g2.fillRoundRect(pos.getXi(), pos.getYi(), s.getXi(), s.getYi(), round, round);
		g2.setColor(Color.black);
		g2.drawRoundRect(pos.getXi(), pos.getYi(), s.getXi(), s.getYi(), round, round);
	}
	
	public Set<GVector2f> getDatas() {
		return datas;
	}
	
}
