package org.chat.gui;

import java.awt.Panel;

import javax.swing.ButtonGroup;
import javax.swing.JButton;
import javax.swing.JLabel;
import javax.swing.JPanel;
import javax.swing.JRadioButton;
import javax.swing.JTextField;

import org.chat.Config;
import org.chat.utils.Log;

public class LoginPanel extends Panel{
	private static final long serialVersionUID = 1L;

	private Gui 			parent;
	private JTextField 		login 		= new JTextField("userName", 15);
	private JTextField 		ip 			= new JTextField(Config.GUI_DEFAULT_ADRESS, 10);
	private JTextField 		port 		= new JTextField(Config.GUI_DEFAULT_PORT, 3);
	private JRadioButton 	guestOption = new JRadioButton("Guest", true);
	private JRadioButton 	hostOption	= new JRadioButton("Host", false);
	private JButton 		start 		= new JButton("Start");
	
	//CONSTRUCTORS
	
	public LoginPanel(Gui parent){
		Log.write("zaèal konštruktor objektu LoginPanel", Log.CONSTRUCTORS);
		this.parent = parent;
		Log.write("skonèil konštruktor objektu LoginPanel", Log.CONSTRUCTORS);
	}
	
	//CREATORS
	
	JPanel createLoginPanel(){
		JPanel panel = new JPanel();
		panel.add(new JLabel("Login"));
		panel.add(login);
		panel.add(start);
		start.addActionListener(a -> parent.getChat().start(login.getText(), 
												  			  ip.getText(),
												  			  port.getText(), 
												  			  hostOption.isSelected()));
		
		return panel;
	}
	
	JPanel createCheckBoxesPanel(){
		JPanel panel = new JPanel();
		ButtonGroup bg = new ButtonGroup();
		
		guestOption.addActionListener(a -> ip.setEnabled(guestOption.isSelected()));
		hostOption.addActionListener(a -> {
			ip.setEnabled(guestOption.isSelected()) ;
			ip.setText(Config.GUI_DEFAULT_ADRESS);
		});
		
		bg.add(guestOption);
		bg.add(hostOption);
		
		panel.add(guestOption);
		panel.add(hostOption);
		
		return panel;
	}

	JPanel createAdressPanel(){
		JPanel panel = new JPanel();
		
		panel.add(new JLabel("IP Adress: "));
		panel.add(ip);
		panel.add(new JLabel("PORT: "));
		panel.add(port);
		
		return panel;
	}

}
