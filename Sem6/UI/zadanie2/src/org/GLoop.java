package org;

public abstract class GLoop {
	private final static double second = 1000000000L;
	private boolean run;
	private boolean VSync;
	private final int fps;
	private final double frameTime;
	
	public GLoop(int fps){
		this(fps, true);
	}

	public GLoop(int fps, boolean VSync){
		this.fps = fps;
		this.VSync = VSync;
		frameTime = second / fps;
	}
	
	public final void start(){
		run = true;
		double loopStart, deltaStart;
		double secondTime = deltaStart = System.nanoTime();
		int frames = 0;
		while(run){
			loopStart = System.nanoTime();
			float delta =(float)(((loopStart - deltaStart)) / frameTime);
			deltaStart = loopStart; 
			loop(delta);
			
			if(VSync)
				Utils.sleep((int)(Math.max(0, frameTime - (System.nanoTime() - loopStart)) / 1000000));
			
			if(System.nanoTime() - secondTime >= second || (VSync && frames >= fps)){
				secondTime = System.nanoTime();
				frames = 0;
				endSecond();
			}
			else{
				frames++;
				endLoop();
			}

		}
		cleanUp();
	}
	
	public final void stop(){
		run = false;
	}
	public void setVSync(boolean vSync) {this.VSync = vSync;}
	
	protected void endSecond(){};
	protected void endLoop(){};
	public abstract void cleanUp();
	protected abstract void loop(float delta);
}