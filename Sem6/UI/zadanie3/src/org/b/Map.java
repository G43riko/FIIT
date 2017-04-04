package org.b;

import java.util.HashSet;
import java.util.Set;

import org.GVector2f;

public class Map{
	private GVector2f size;
	private Set<GVector2f> allTreasures = new HashSet<GVector2f>();
	
	public Map(GVector2f size, GVector2f ... treasures){
		this.size = size;
		for(GVector2f t : treasures)
			this.allTreasures.add(t);
			
				
	}

	public void solve(Path path, GVector2f pos){
		int findTreasures = 0;
		int steps = 0;
		String source = path.getPath();
		Set<GVector2f> treasures = new HashSet<GVector2f>(allTreasures); 
		do{
			if(source.startsWith("h"))
				pos.addToY(1);
			else if(source.startsWith("d"))
				pos.addToY(-1);
			else if(source.startsWith("l"))
				pos.addToX(-1);
			else if(source.startsWith("p"))
				pos.addToX(1);
			
			if(!pos.isInRect(new GVector2f(), size.add(1))){
				path.solve(findTreasures, allTreasures.size(), steps, (int)size.mul());
				return;
			}
			
			steps++;
			if(treasures.contains(pos)){
				treasures.remove(pos);
				findTreasures++;
			}
				
			source = source.substring(1, source.length());
		}while(!source.isEmpty());

//		System.out.println("na�iel som " + findTreasures + " pokladov");
		path.solve(findTreasures, allTreasures.size(), steps, (int)size.mul());
	}
	
	public int getTiles(){
		return (int)size.mul();
	}
}
