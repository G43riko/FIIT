package org.b;

public class Path{
	private int treasures;
	public int[] getSource() {
		return source;
	}


	private int maxTreasures;
	private int steps;
	private String path;
	private float fitness = 0;
	private boolean solved;
	private boolean build;
	private int[] source;
	private int mapSize;
	
	public Path(int[] source) {
		this.source = source;
	}
	
	public void setPath(String path) {
		this.path = path;
		build = true;
	}

	public void solve(int treasures, int maxTreasures, int steps, int mapSize){
		this.maxTreasures = maxTreasures;
		this.treasures = treasures;
		this.mapSize = mapSize;
		this.steps = steps;
		
		fitness = 30 - (float)steps / (float)mapSize * 30 + (float)treasures / (float)maxTreasures * 70;
		
		solved = true;
	}
	
	public float getFitness() {
		return fitness;
	}

	public String getPath() {
		return path;
	}
	
	public int getTreasures() {
		return treasures;
	}

	public int getSteps() {
		return steps;
	}

	public boolean isSolved() {
		return solved;
	}


	public boolean isBuild() {
		return build;
	}

	@Override
	public String toString() {
		return "treasures: " + treasures + "/" + maxTreasures + ", steps: " + steps + ", fitness :" + fitness + ", size: " + mapSize;
	}
}
