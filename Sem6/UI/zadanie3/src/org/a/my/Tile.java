package org.a.my;

import java.text.DecimalFormat;

import org.GVector2f;

public class Tile{
	private GVector2f position;
	private int distFromBorder;
	private char visited = '0';
	private Tile parent;
	private char from = '0';
	private boolean rock;
	
	public Tile(GVector2f position, int distFromBorder){
		this(position, distFromBorder, false);
	}
	
	public Tile(GVector2f position, int distFromBorder, boolean rock) {
		this.position = new GVector2f(position);
		this.distFromBorder = distFromBorder;
		this.rock = rock;
	}
	
	public boolean isRock() {
		return rock;
	}

	public Tile getParent() {
		return parent;
	}

	public void setParent(Tile parent) {
		this.parent = parent;
	}

	public char getFrom() {return from;}
	public char getVisited() {return visited;}
	public GVector2f getPosition() {return position;}
	public int calcBorderDist() {return distFromBorder;}

	public void setVisited(char visited) {this.visited = visited;}
	public Tile setFrom(char from) {this.from = from; return this;}
	public void setPosition(GVector2f position) {this.position = position;}
	public void setDistFromBorder(int distFromBorder) {this.distFromBorder = distFromBorder;}

	@Override
	public String toString() {
		return position.toString() + " from: " + from;
	}
}
