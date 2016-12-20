import java.awt.*;

import java.awt.event.FocusEvent;
import java.awt.event.FocusListener;
import java.awt.event.KeyEvent;
import java.awt.event.KeyListener;

import javax.script.ScriptEngine;
import javax.script.ScriptEngineManager;
import javax.swing.*;

public class Aei extends JComponent implements KeyListener {
	JFrame frame;
	String stringarray[][] = new String[11][11];
	String check[][] = new String[11][11];
	JTextField[][] field = new JTextField[11][11];
	JTextField upfield;
	JTextField fx;
	JPanel panel, uppanel;
	int x;
	int y;
	String cellname[][]=new String[11][11];

	public void cellnaming(){
		for(int i=1;i<11;i++){
			cellname[1][i]="A"+i;
			cellname[2][i]="B"+i;
			cellname[3][i]="C"+i;
			cellname[3][i]="C"+i;
			cellname[4][i]="D"+i;
			cellname[5][i]="E"+i;
			cellname[6][i]="F"+i;
			cellname[7][i]="G"+i;
			cellname[8][i]="H"+i;
			cellname[9][i]="I"+i;
			cellname[10][i]="J"+i;
		}
		
		
		
	}
	public Aei() {
		cellnaming();
		if(validate3("=C10")){
			System.out.print("asdfs");
			
		}
		frame = new JFrame();
		frame.setLayout(null);
		uppanel = new JPanel();
		uppanel.setLayout(null);
		upfield = new JTextField();
		fx = new JTextField();
		uppanel.add(upfield);
		uppanel.add(fx);
		upfield.setBounds(120, 10, 400, 30);
		fx.setBounds(10, 10, 100, 30);
		fx.setText("FX");

		fx.setBackground(Color.LIGHT_GRAY);
		fx.setForeground(Color.BLACK);
		fx.setEditable(false);
		upfield.setBackground(Color.LIGHT_GRAY);
		upfield.setForeground(Color.BLACK);
		upfield.setEditable(false);
		upfield.addKeyListener(this);

		panel = new JPanel();

		frame.setSize(630, 450);
		frame.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
		panel.setLayout(new GridLayout(11, 11));

		for (int i = 0; i < 11; i++) {
			for (int j = 0; j < 11; j++) {
				field[i][j] = new JTextField();
			}

		}

		for (int i = 0; i < 11; i++) {
			for (int j = 0; j < 11; j++) {
				panel.add(field[i][j]);

				field[i][j].addKeyListener(this);

			}

		}
		for (int i = 1; i < 11; i++) {
			for (int j = 1; j < 11; j++) {
				stringarray[i][j] = "11";

			}

		}
		for (int i = 1; i < 11; i++) {
			for (int j = 1; j < 11; j++) {
				check[i][j] = "11";

			}

		}

		field[0][1].setText("A");
		field[0][2].setText("B");
		field[0][3].setText("C");
		field[0][4].setText("D");
		field[0][5].setText("E");
		field[0][6].setText("F");
		field[0][7].setText("G");
		field[0][8].setText("H");
		field[0][9].setText("I");
		field[0][10].setText("J");
		field[0][0].setEditable(false);
		field[0][1].setEditable(false);
		field[0][2].setEditable(false);
		field[0][3].setEditable(false);
		field[0][4].setEditable(false);
		field[0][5].setEditable(false);
		field[0][6].setEditable(false);
		field[0][7].setEditable(false);
		field[0][8].setEditable(false);
		field[0][9].setEditable(false);
		field[0][10].setEditable(false);

		for (int i = 1; i < 11; i++) {

			field[i][0].setText("" + i);
			field[i][0].setEditable(false);

		}
		frame.addKeyListener(this);
		frame.add(uppanel);
		uppanel.setBounds(0, 0, 500, 50);

		frame.add(panel);
		panel.setBounds(0, 50, 600, 350);
		x = 1;
		y = 1;
		field[x][y].setBackground(Color.YELLOW);

		frame.setVisible(true);

	}

	public void keyPressed(KeyEvent e) {

		if (e.getKeyCode() == KeyEvent.VK_UP) {
			if (x > 1) {
				field[x][y].setBackground(Color.WHITE);
				x = x - 1;
				field[x][y].setForeground(Color.RED);
				field[x][y].grabFocus();
				field[x][y].setBackground(Color.YELLOW);
			}
		}
		if (e.getKeyCode() == KeyEvent.VK_DOWN) {
			if (x < 10) {
				field[x][y].setBackground(Color.WHITE);
				x = x + 1;
				field[x][y].grabFocus();
				field[x][y].setForeground(Color.RED);
				field[x][y].setBackground(Color.YELLOW);
			}
		}

		if (e.getKeyCode() == KeyEvent.VK_LEFT) {
			if (y > 1) {
				field[x][y].setBackground(Color.WHITE);

				y = y - 1;
				field[x][y].grabFocus();
				field[x][y].setForeground(Color.RED);
				field[x][y].setBackground(Color.YELLOW);
			}
		}
		if (e.getKeyCode() == KeyEvent.VK_RIGHT) {
			if (y < 10) {
				field[x][y].setBackground(Color.WHITE);

				y = y + 1;
				field[x][y].grabFocus();
				field[x][y].setForeground(Color.RED);
				field[x][y].setBackground(Color.YELLOW);
			}
		}
		if (e.getKeyCode() == KeyEvent.VK_ENTER) {
			if(validate3(field[x][y].getText())){
				
				String str = field[x][y].getText();
				int length=str.length();
				JOptionPane.showMessageDialog(null, "hehehehe");
				for (int i = 1; i < length-2; i++){
					
					if(cellcheck(str.substring(i,i+3).toUpperCase())){
						
						int tem=abc(str.charAt(i));
						
						int tem2=Integer.valueOf(str.substring(i+1,i+3));
						JOptionPane.showMessageDialog(null, tem2);
						String t=field[tem2][tem].getText();
						JOptionPane.showMessageDialog(null, t);
						
						String temp=str.replaceAll(str.substring(i,i+3),t );
						str=temp;
						length=str.length();
						
					
					
					}else if(cellcheck(str.substring(i,i+2).toUpperCase())){
						int tem=abc(str.charAt(i));
						JOptionPane.showMessageDialog(null, tem);
						int tem2=Integer.valueOf(str.substring(i+1,i+2));
						String temp=str.replaceAll(str.substring(i,i+2),field[tem2][tem].getText() );
						str=temp;
						
							
					}
				
				}
				String inivalue = str.substring(1, str.length());
				int value=0;
				try {
					value=stringtoint(inivalue);
				} catch (Exception e1) {
					// TODO Auto-generated catch block
					e1.printStackTrace();
				}
				stringarray[x][y] = field[x][y].getText();
				field[x][y].setText("" + value);
				check[x][y] = field[x][y].getText();

				
			}
			
			
			if (validate2(field[x][y].getText())) {
				String str = field[x][y].getText();
				String inivalue = str.substring(1, str.length());
				int value = 0;
				try {
					value = stringtoint(inivalue);
				} catch (Exception e1) {
					// TODO Auto-generated catch block
					e1.printStackTrace();
				}
				stringarray[x][y] = field[x][y].getText();
				field[x][y].setText("" + value);
				check[x][y] = field[x][y].getText();

			}

			if (validate(field[x][y].getText())) {

				String str = field[x][y].getText();
				int z1, z2, z3, z4;
				z1 = str.indexOf("(");
				z2 = str.indexOf(")");
				z3 = str.indexOf(":");
				z4 = str.indexOf(",");
				if (Character.toUpperCase(str.charAt(1)) == 'S') {
					int value = sum(str.charAt(z1 + 1),
							Integer.valueOf(str.substring(z1 + 2, z3)),
							str.charAt(z3 + 1),
							Integer.valueOf(str.substring(z3 + 2, z2)));
					stringarray[x][y] = field[x][y].getText();
					field[x][y].setText("" + value);
					check[x][y] = field[x][y].getText();

				} else if (Character.toUpperCase(str.charAt(1)) == 'A') {
					int value = average(str.charAt(z1 + 1),
							Integer.valueOf(str.substring(z1 + 2, z3)),
							str.charAt(z3 + 1),
							Integer.valueOf(str.substring(z3 + 2, z2)));
					stringarray[x][y] = field[x][y].getText();
					field[x][y].setText("" + value);
					check[x][y] = field[x][y].getText();

				} else if (Character.toUpperCase(str.charAt(1)) == 'M'
						&& Character.toUpperCase(str.charAt(2)) == 'I') {
					int value = min(str.charAt(z1 + 1),
							Integer.valueOf(str.substring(z1 + 2, z3)),
							str.charAt(z3 + 1),
							Integer.valueOf(str.substring(z3 + 2, z2)));
					stringarray[x][y] = field[x][y].getText();
					field[x][y].setText("" + value);
					check[x][y] = field[x][y].getText();

				} else if (Character.toUpperCase(str.charAt(1)) == 'M'
						&& Character.toUpperCase(str.charAt(2)) == 'A') {
					int value = max(str.charAt(z1 + 1),
							Integer.valueOf(str.substring(z1 + 2, z3)),
							str.charAt(z3 + 1),
							Integer.valueOf(str.substring(z3 + 2, z2)));
					stringarray[x][y] = field[x][y].getText();
					field[x][y].setText("" + value);
					check[x][y] = field[x][y].getText();

				} else if (Character.toUpperCase(str.charAt(1)) == 'C') {
					int value = count(str.charAt(z1 + 1),
							Integer.valueOf(str.substring(z1 + 2, z3)),
							str.charAt(z3 + 1),
							Integer.valueOf(str.substring(z3 + 2, z4)),
							Integer.valueOf(str.substring(z4 + 1, z2)));
					stringarray[x][y] = field[x][y].getText();
					field[x][y].setText("" + value);
					check[x][y] = field[x][y].getText();

				}
				JOptionPane.showMessageDialog(null, "formula");
				JOptionPane.showMessageDialog(null, "correct formula");
				// int a=sum('A',1,'B',1);
				// System.out.print(""+a);
			} else {

			}
			if (x < 10) {
				field[x][y].setBackground(Color.WHITE);

				x = x + 1;
				field[x][y].grabFocus();
				field[x][y].setForeground(Color.RED);
				field[x][y].setBackground(Color.YELLOW);
			}

		}

	}

	public int sum(char s1, int j1, char s2, int j2) {
		int i1 = abc(s1);
		int i2 = abc(s2);
		int sum = 0;
		for (int i = i1; i < i2 + 1; i++) {
			for (int j = j1; j < j2 + 1; j++) {
				if (isInteger(field[i][j].getText())) {
					sum = sum + Integer.valueOf(field[i][j].getText());
				}
			}

		}

		return sum;
	}

	public int average(char s1, int j1, char s2, int j2) {
		int count = 0;
		int i1 = abc(s1);
		int i2 = abc(s2);
		int sum = 0;
		for (int i = i1; i < i2 + 1; i++) {
			for (int j = j1; j < j2 + 1; j++) {
				if (isInteger(field[i][j].getText())) {
					sum = sum + Integer.valueOf(field[i][j].getText());
					count = count + 1;
				}
			}

		}

		return sum / count;
	}

	public int min(char s1, int j1, char s2, int j2) {

		int i1 = abc(s1);
		int i2 = abc(s2);
		int count = ((i2 - i1) + 1) * ((j2 - j1) + 1);
		int array[] = new int[count];
		int tnu = 0;

		for (int i = i1; i < i2 + 1; i++) {
			for (int j = j1; j < j2 + 1; j++) {
				if (isInteger(field[i][j].getText())) {
					array[tnu] = Integer.valueOf(field[i][j].getText());
					tnu = tnu + 1;
				}

			}

		}

		return smallest(array, tnu);

	}

	public int smallest(int[] sma, int tnu) {

		int small = sma[0];
		for (int i = 0; i < tnu - 1; i++) {
			if (small > sma[i + 1]) {
				small = sma[i + 1];

			}

		}

		return small;

	}

	public int max(char s1, int j1, char s2, int j2) {

		int i1 = abc(s1);
		int i2 = abc(s2);
		int count = ((i2 - i1) + 1) * ((j2 - j1) + 1);
		int array[] = new int[count];
		int tnu = 0;
		for (int i = i1; i < i2 + 1; i++) {
			for (int j = j1; j < j2 + 1; j++) {
				if (isInteger(field[i][j].getText())) {
					array[tnu] = Integer.valueOf(field[i][j].getText());
					tnu = tnu + 1;
				}

			}

		}

		return biggest(array, tnu);

	}

	public int biggest(int[] sma, int tnu) {

		int small = sma[0];
		for (int i = 0; i < tnu - 1; i++) {
			if (small < sma[i + 1]) {
				small = sma[i + 1];

			}

		}

		return small;

	}

	public int count(char s1, int j1, char s2, int j2, int k) {

		int i1 = abc(s1);
		int i2 = abc(s2);
		int count = ((i2 - i1) + 1) * ((j2 - j1) + 1);
		int array[] = new int[count];
		int tnu = 0;
		for (int i = i1; i < i2 + 1; i++) {
			for (int j = j1; j < j2 + 1; j++) {
				array[tnu] = Integer.valueOf(field[i][j].getText());
				tnu = tnu + 1;

			}

		}

		return counted(array, k);

	}

	public int counted(int[] array, int k) {
		int length = array.length;
		int inc = 0;
		for (int i = 0; i < length; i++) {
			if (k == array[i]) {
				inc++;

			}

		}
		return inc;

	}

	public int abc(char i) {
		char a;
		a = Character.toUpperCase(i);
		if (a == 'A') {
			return 1;
		} else if (a == 'B') {

			return 2;
		} else if (a == 'C') {

			return 3;
		} else if (a == 'D') {

			return 4;
		} else if (a == 'E') {

			return 5;
		} else if (a == 'F') {

			return 6;
		} else if (a == 'G') {

			return 7;
		} else if (a == 'H') {

			return 8;
		} else if (a == 'I') {

			return 9;
		} else if (a == 'J') {

			return 10;
		} else {

			return 0;
		}

	}

	public static void main(String[] args) {
		Aei ei = new Aei();
	}

	@Override
	public void keyReleased(KeyEvent e) {

		String s = field[x][y].getText();
		upfield.setText(s);
		if (stringarray[x][y] != "11") {
			upfield.setText(stringarray[x][y]);

		}
		if (field[x][y].getText().equals(check[x][y])) {

		} else {
			upfield.setText(s);
			stringarray[x][y] = "11";
			check[x][y] = "11";
		}

	}

	@Override
	public void keyTyped(KeyEvent e) {
		// TODO Auto-generated method stub

	}

	public int stringtoint(String s) throws Exception {
		ScriptEngineManager factory = new ScriptEngineManager();
		ScriptEngine engine = factory.getEngineByName("JavaScript");
		int result = ((Double) engine.eval(s)).intValue();
		return result;

	}
	
	public boolean cellcheck(String s){
		for(int i=1;i<11;i++){
			for(int j=1;j<11;j++){
				if(s.equals(cellname[i][j])){
					return true;
					
				}
				
				
			}
			
		}
		return false;
	}
	public boolean validate3(String str){
		int length = str.length();
		boolean b=false;
		if (str.substring(0, 1).equals("=")){
			System.out.println(str.substring(0,1));
			for (int i = 0; i < length-2; i++){
				System.out.println(str.substring(i,i+3));
				if(cellcheck(str.substring(i,i+3).toUpperCase())){
					JOptionPane.showMessageDialog(null, "hehehehe");
				System.out.print("1");
				b=true;
				
				}else if(cellcheck(str.substring(i,i+2).toUpperCase())){
					JOptionPane.showMessageDialog(null, "hehehehe");	
					b=true;	
				}else if(Character.isDigit(str.charAt(i))){
					b=true;
					
				}else if(str.substring(i,i+1).equals("+")
						||str.substring(i,i+1).equals("-")
						||str.substring(i,i+1).equals("*")
						||str.substring(i,i+1).equals("/")
						||str.substring(i,i+1).equals("(")
						||str.substring(i,i+1).equals(")")){
					b=true;
					
				}else {
					
					return false;
				}
				
				
			}
			
			
		}
		
		if(b){
			return true;
		}else{
			return false;
		}
		
	}

	public boolean validate2(String str) {
		int length = str.length();
		if (str.substring(0, 1).equals("=")) {
			for (int i = 1; i < length; i++) {
				if (Character.isDigit(str.charAt(i))
						|| str.substring(i, i + 1).equals("+")
						|| str.substring(i, i + 1).equals("-")
						|| str.substring(i, i + 1).equals("*")
						|| str.substring(i, i + 1).equals("/")
						|| str.substring(i, i + 1).equals("(")
						|| str.substring(i, i + 1).equals(")")) {
					String temp = str.substring(1, length);
					try {
						int value = stringtoint(temp);

						return true;
					} catch (Exception e) {
						return false;
					}

				}

			}

		}
		return false;

	}

	public boolean validate(String str) {
		try {

			boolean a, b, c;
			a = false;
			b = false;
			c = false;

			a = str.startsWith("=");
			int z1, z2, z3, z4;
			z1 = str.indexOf("(");
			z2 = str.indexOf(")");
			z3 = str.indexOf(":");
			z4 = str.indexOf(",");
			String sub;
			sub = str.substring(1, z1);
			System.out.print(sub.toUpperCase());
			if (sub.toUpperCase().equals("SUM")
					|| sub.toUpperCase().equals("AVERAGE")
					|| sub.toUpperCase().equals("MIN")
					|| sub.toUpperCase().equals("MAX")
					|| sub.toUpperCase().equals("COUNTIF")) {

				b = true;

			}
			if (z1 != -1 && z2 != -1 && z3 != -1 && z4 != -1) {
				if (Character.isLetter(str.charAt(z1 + 1))
						&& Character.isLetter(str.charAt(z3 + 1))
						&& Integer.valueOf(str.substring(z1 + 2, z3)) < 11
						&& Integer.valueOf(str.substring(z3 + 2, z4)) < 11
						&& isInteger(str.substring(z4 + 1, z2)))

				{
					JOptionPane.showMessageDialog(null, "sddf");
					c = true;

				}

			}

			else if (z1 != -1 && z2 != -1 && z3 != -1) {
				if (Character.isLetter(str.charAt(z1 + 1))
						&& Character.isLetter(str.charAt(z3 + 1))
						&& Integer.valueOf(str.substring(z1 + 2, z3)) < 11
						&& Integer.valueOf(str.substring(z3 + 2, z2)) < 11)

				{
					c = true;

				}
			}

			if (a && b && c) {
				return true;

			} else {

				return false;
			}
		} catch (Exception ex) {
			return false;
		}

	}

	public boolean isInteger(String s) {
		try {
			int a = Integer.valueOf(s);
			return true;
		} catch (Exception d) {
			return false;

		}

	}

}
