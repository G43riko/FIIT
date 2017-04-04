package org;

import java.io.Serializable;

public class GVector2f implements Serializable{
	private static final long serialVersionUID = 1L;
	
	private float x,y;
	
	public GVector2f(){
		this(0,0);
	};
	
	public GVector2f(float num){
		this(num, num);
	};
	
	public GVector2f(float x, float y){
		this.x = x;
		this.y = y;
	};
	
	public GVector2f(String s){
		s = s.replaceAll("[\\[()\\]]", "").replaceAll("[x,]", "_");
		String[] strings = s.split("_");
		
		this.x = Float.parseFloat(strings[0]);
		this.y = Float.parseFloat(strings[1]);
	};
	
	public GVector2f(double x, double y){
		this.x = (float)x;
		this.y = (float)y;
	};
	
	public GVector2f(GVector2f v){
		this.x = v.x;
		this.y = v.y;
	};
	
	public float dot(GVector2f v){
        return x * v.x + y * v.y;
    };
    
    public boolean atLeastOneSame(GVector2f v){
    	return v.x == x || v.y == y;
    }
    
	public float getLength(){
		return (float)Math.sqrt(this.x*this.x+this.y*this.y);
	};
	
	public GVector2f max(GVector2f v){
		return new GVector2f(Math.max(v.getX(), getX()), Math.max(v.getY(), getY()));
	}
	
	public float max(){
		return Math.max(this.x,this.y);
	};
	
	public GVector2f min(GVector2f v){
		return new GVector2f(Math.min(v.getX(), getX()), Math.min(v.getY(), getY()));
	}
	
	public float min(){
		return Math.min(this.x,this.y);
	};
	
	public void normalize(){
		float dlzka = this.getLength();
		this.x /= dlzka;
		this.y /= dlzka;
	}
	
	public GVector2f Normalized(){
		float dlzka = this.getLength();
		return new GVector2f(x/dlzka, y/dlzka);
	}
	
	public float cross(GVector2f r){
		return x * r.y - y * r.x;
	}
	
	public void rotate(float angle){
		float rad=(float)Math.toRadians(angle);

		float cos=(float)Math.cos(rad);
		float sin=(float)Math.sin(rad);
		this.x = (x * cos - y * sin);
		this.y = (x * sin + y * cos);
	}
	
	public float dist(GVector2f v){
		//return distance between 2 point
		float dx = x - v.x;
		float dy = y - v.y;
		return (float)Math.sqrt(dx * dx + dy * dy);
	}
	
	public GVector2f Lerp(GVector2f dest, float lerpFactor)
	{
		return dest.sub(this).mul(lerpFactor).add(this);
	}
	
	public float distSQ(GVector2f v) {
		float distX = x - v.x;
		float distY = y - v.y;
		return distX * distX + distY * distY;
	}
	
	public GVector2f negate(){
		this.x *= -1;
		this.y *= -1;
		return this;
	}
	
	public float angleBetween(GVector2f v) {
		float dotProduct = dot(v);
		float angle = (float)Math.acos(dotProduct);
		return angle;
	}
	
	public GVector2f add(GVector2f v){
		return new GVector2f(x + v.x, y + v.y);
	};
	
	public GVector2f add(float num){
		return new GVector2f(x + num, y + num);
	};
	
	public GVector2f sub(GVector2f v){
		return new GVector2f(x - v.x, y - v.y);
	};
	
	public GVector2f sub(float num){
		return new GVector2f(x - num, y - num);
	};
	
	public GVector2f mul(GVector2f v){
		return new GVector2f(x * v.x, y * v.y);
	};
	
	public GVector2f mul(float num){
		return new GVector2f(x * num, y * num);
	}
	
	public GVector2f div (GVector2f v){
		return new GVector2f(x / v.x, y / v.y);
	};
	
	public GVector2f div (float num){
		return new GVector2f(x / num, y / num);
	};

	public GVector2f mod(GVector2f v) {
		return new GVector2f(x % v.x, y % v.y);
	};
	
	public GVector2f mod(float num) {
		return new GVector2f(x % num, y % num);
	};
	
	public GVector2f abs(){
		return new GVector2f(Math.abs(x), Math.abs(y));
	};

	public float average(){
		return (x + y) / 2;
	}
	
	public float sum(){
		return x + y;
	}
	
	public boolean isNull(){
		return x == 0 && y == 0;
	};
	
	public float mul() {return x * y;}
	
	public float getX() {return x;}
	public float getY() {return y;}

	public int getXi() {return (int)x;}
	public int getYi() {return (int)y;}
	
	public void setX(float x) {this.x = x;}
	public void setY(float y) {this.y = y;}
	
	public GVector2f addToY(float y){this.y += y; return this;}
	public GVector2f addToX(float x){this.x += x; return this;}
	
	public void set(float x, float y){this.x = x;this.y = y;}
	
	public void set(GVector2f a){set(a.x, a.y);}
	
	public String toString(){
		return "["+this.x+"x"+this.y+"]";
	}

	public GVector2f toInt(){return new GVector2f((int)x, (int)y);}

	public GVector2f getInstatnce(){
		return new GVector2f(this);
	}
	
	public boolean equals(Object obj){
		if(!(obj instanceof GVector2f))
			return false;
		GVector2f v = (GVector2f)obj;
		
		return x == v.x && y == v.y;
	};
	
	@Override
	public int hashCode() {
		int sum = getXi() + getYi();
		return sum * (sum + 1) / 2 + getXi();
	}
	
	public String toDecimal(int i) {
		return "[" + String.format("%0" +  i + "d ",(int)x) + "x" + String.format("%0" + i + "d ",(int)y) + "]";
	};
	
	public boolean isInRect(GVector2f aPos, GVector2f aSize){
		return x > aPos.x && x < aPos.x + aSize.x && y > aPos.y && y < aPos.y + aSize.y;
	}
}