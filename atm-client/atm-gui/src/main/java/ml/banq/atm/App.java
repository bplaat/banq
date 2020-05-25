package ml.banq.atm;

import com.fazecast.jSerialComm.SerialPort;
import com.fazecast.jSerialComm.SerialPortEvent;
import com.fazecast.jSerialComm.SerialPortMessageListener;
import java.awt.event.ComponentEvent;
import java.awt.event.ComponentListener;
import java.awt.image.BufferedImage;
import java.awt.Cursor;
import java.awt.GraphicsEnvironment;
import java.awt.Point;
import java.awt.Toolkit;
import java.util.HashMap;
import javax.swing.JDialog;
import javax.swing.JFrame;
import javax.swing.JOptionPane;
import javax.swing.UIManager;
import javax.swing.SwingUtilities;
import org.json.JSONObject;

// The singleton App root class
public class App implements Runnable, ComponentListener, SerialPortMessageListener {
    // The app singleton instance
    private static App instance = new App();

    private JFrame frame;
    private SerialPort serialPort;

    // Mute beeper toggle variable when in debug mode mute automatic
    private boolean muteBeeper = !Config.DEBUG;

    private App() {}

    // A method to get a App intance
    public static App getInstance() {
        return instance;
    }

    // The run method that creates the UI
    public void run() {
        // Print intro message
        Log.info("Banq ATM GUI");

        // Select the native UI theme for Java Swing
        try {
            UIManager.setLookAndFeel(UIManager.getSystemLookAndFeelClassName());
        } catch (Exception exception) {
            Log.warning(exception);
        }

        // Open a serial port
        SerialPort[] serialPorts = SerialPort.getCommPorts();
        if (serialPorts.length > 0) {
			
			// Choose first port by default
			SerialPort chosenPort = serialPorts[0];
			
			// If a port has been specified in the config, search for a port that matches that port
			if (!Config.SERIAL_PORT.equals("")) {
				boolean portFound = false;
				
				for (SerialPort port : serialPorts) {
					if (port.getSystemPortName().equals(Config.SERIAL_PORT)) {
						chosenPort = port;
						portFound = true;
						break;
					}
				}
				
				// If no port with that name has been found, show a message and keep using the first port
				if (!portFound) {
					Log.debug("No port with name " + Config.SERIAL_PORT + " was found. Defaulting to first port.");
				}
			}
			
            Log.debug("Opening serial port " + chosenPort.getSystemPortName());
            serialPort = chosenPort;
            serialPort.openPort();
            serialPort.addDataListener(this);
        }

        // Show an error message when not connected
        else {
            // Show error dialog
            Toolkit.getDefaultToolkit().beep();
            JOptionPane optionPane = new JOptionPane("Can't connect with a serial port!", JOptionPane.ERROR_MESSAGE);
            JDialog dialog = optionPane.createDialog("Serial Port Error");
            dialog.setIconImage(ImageUtils.loadImage("logo.png", 96, 96).getImage());
            dialog.setAlwaysOnTop(true);
            dialog.setVisible(true);

            // Print error log message and exit
            Log.error("Can't connect with a serial port!");
        }

        // Create the Java Swing window
        frame = new JFrame("Banq ATM GUI");
        frame.setIconImage(ImageUtils.loadImage("logo.png", 96, 96).getImage());
        frame.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        if (Config.FULLSCREEN_MODE) {
            frame.setUndecorated(true);
            GraphicsEnvironment.getLocalGraphicsEnvironment().getScreenDevices()[0].setFullScreenWindow(frame);
            hideCursor();
        } else {
            frame.setSize(1280, 1024);
            frame.setLocationRelativeTo(null);
        }
        frame.addComponentListener(this);

        // Add the navigator system to the java swing class
        frame.add(Navigator.getInstance());
        Navigator.getInstance().changePage(new WelcomePage(), false);

        // Made the window visible
        frame.setVisible(true);
    }

    // Listen to resize and move events of the window to resize the navigator
    public void componentShown(ComponentEvent event) {}

    public void componentHidden(ComponentEvent event) {}

    public void componentMoved(ComponentEvent event) {
        Navigator.getInstance().resizePage(frame.getWidth(), frame.getHeight());
    }

    public void componentResized(ComponentEvent event) {
        Navigator.getInstance().resizePage(frame.getWidth(), frame.getHeight());
    }

    // Set up some options for the serial port listener
    public int getListeningEvents() {
        return SerialPort.LISTENING_EVENT_DATA_RECEIVED;
    }

    public byte[] getMessageDelimiter() {
        return new byte[] { 0x0a };
    }

    public boolean delimiterIndicatesEndOfMessage() {
        return true;
    }

    // Listen to serial port data messages
    public void serialEvent(SerialPortEvent event) {
        if (event.getEventType() == SerialPort.LISTENING_EVENT_DATA_RECEIVED) {
            String line = new String(event.getReceivedData());
            Log.debug("Read: " + line);
            if (line.charAt(0) == '{') {
                try {
                    JSONObject message = new JSONObject(line);
                    SwingUtilities.invokeLater(() -> {
                        // Give all the events to the current page of the navigator
                        if (message.getString("type").equals("keypad")) {
                            String key = message.getString("key");
                            if (key.equals("A")) {
                                muteBeeper = !muteBeeper;
                                Navigator.getInstance().showMuteImage(muteBeeper);
                            }
                            else if (key.equals("C")) {
                                Navigator.getInstance().changePage(new WelcomePage());
                            }
                            else {
                                Navigator.getInstance().getPage().onKeypad(key);
                            }
                        }

                        if (message.getString("type").equals("rfid_read")) {
                            Navigator.getInstance().getPage().onRFIDRead(message.getString("account_id"), message.getString("rfid_uid"));
                        }

                        if (message.getString("type").equals("rfid_write")) {
                            Navigator.getInstance().getPage().onRFIDWrite(message.getString("account_id"), message.getString("rfid_uid"));
                        }

                        if (message.getString("type").equals("money")) {
                            Navigator.getInstance().getPage().onMoney();
                        }

                        if (message.getString("type").equals("printer")) {
                            Navigator.getInstance().getPage().onPrinter();
                        }
                    });
                } catch (Exception exception) {
                    Log.error(exception);
                }
            }
        }
    }

    // A method that shows the default cursor
    void showCursor() {
        frame.setCursor(Cursor.getDefaultCursor());
    }

    // A method that hides the cursor
    void hideCursor() {
        frame.setCursor(frame.getToolkit().createCustomCursor(new BufferedImage(1, 1, BufferedImage.TYPE_INT_ARGB), new Point(), null));
    }

    // A method that returns the window width
    public int getWindowWidth() {
        return frame.getWidth();
    }

    // A method that returns to window height
    public int getWindowHeight() {
        return frame.getHeight();
    }

    // A method that forces the window to repaint
    public void repaintWindow() {
        frame.getContentPane().validate();
        frame.getContentPane().repaint();
    }

    // A method that sends a RFID write message
    public void sendWriteRFID(String account_id) {
        JSONObject message = new JSONObject();
        message.put("type", "rfid_write");
        message.put("account_id", account_id);
        sendMessage(message);
    }

    // A method that sends a beeper message
    public void sendBeeper(int frequency, int duration) {
        if (!muteBeeper) {
            JSONObject message = new JSONObject();
            message.put("type", "beeper");
            message.put("frequency", frequency);
            message.put("duration", duration);
            sendMessage(message);
        }
    }

    // A method that sends a printer message
    public void sendPrinter(String[] lines) {
        JSONObject message = new JSONObject();
        message.put("type", "printer");
        message.put("lines", lines);
        sendMessage(message);
    }

    // A method that sends a money dispencer message
    public void sendMoney(HashMap<String, Integer> money) {
        JSONObject message = new JSONObject();
        message.put("type", "money");
        message.put("money", money);
        sendMessage(message);
    }

    // A method that writes the message to the serial port
    public void sendMessage(JSONObject message) {
        String line = message.toString() + "\n";
        byte[] bytes = line.getBytes();
        serialPort.writeBytes(bytes, bytes.length);
        Log.debug("Write: " + line);
    }
}
