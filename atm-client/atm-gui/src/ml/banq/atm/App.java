package ml.banq.atm;

import com.fazecast.jSerialComm.SerialPort;
import com.fazecast.jSerialComm.SerialPortEvent;
import com.fazecast.jSerialComm.SerialPortMessageListener;
import java.awt.event.ComponentEvent;
import java.awt.event.ComponentListener;
import java.awt.Color;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.UIManager;
import javax.swing.SwingUtilities;
import org.json.JSONObject;

public class App implements Runnable, ComponentListener, SerialPortMessageListener {
    private static App instance = new App();

    private JFrame frame;
    private SerialPort serialPort;
    private JLabel infoLabel;

    private App() {}

    public static App getInstance() {
        return instance;
    }

    public void run() {
        BanqAPI.getInstance().setKey(Config.DEVICE_KEY);
        Language.getInstance().changeLanguage(Config.LANGUAGES[0]);

        try {
            UIManager.setLookAndFeel(UIManager.getSystemLookAndFeelClassName());
        } catch (Exception e) {}

        frame = new JFrame("Banq ATM GUI");
        frame.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        if (Config.FULLSCREEN_MODE) {
            frame.setResizable(false);
            frame.setExtendedState(JFrame.MAXIMIZED_BOTH);
            frame.setUndecorated(true);
        } else {
            frame.setSize(1280, 1024);
            frame.setLocationRelativeTo(null);
        }
        frame.addComponentListener(this);
        frame.setVisible(true);

        frame.add(Navigator.getInstance());
        Navigator.getInstance().changePage(new WelcomePage(), false);

        SerialPort[] serialPorts = SerialPort.getCommPorts();
        if (serialPorts.length > 0) {
            serialPort = serialPorts[0];
            serialPort.openPort();
            serialPort.addDataListener(this);
        }
    }

    public void componentShown(ComponentEvent event) {}
    public void componentHidden(ComponentEvent event) {}

    public void componentMoved(ComponentEvent event) {
        Navigator.getInstance().resizePage(frame.getWidth(), frame.getHeight());
    }

    public void componentResized(ComponentEvent event) {
        Navigator.getInstance().resizePage(frame.getWidth(), frame.getHeight());
    }

    public int getListeningEvents() {
        return SerialPort.LISTENING_EVENT_DATA_RECEIVED;
    }

    public byte[] getMessageDelimiter() {
        return new byte[] { 0x0a };
    }

    public boolean delimiterIndicatesEndOfMessage() {
        return true;
    }

    public void serialEvent(SerialPortEvent event) {
        if (event.getEventType() == SerialPort.LISTENING_EVENT_DATA_RECEIVED) {
            String line = new String(event.getReceivedData());
            System.out.print("[INFO] Read: " + line);
            if (line.charAt(0) == '{') {
                try {
                    JSONObject message = new JSONObject(line);
                    SwingUtilities.invokeLater(new Runnable() {
                        public void run() {
                            if (message.getString("type").equals("keypad")) {
                                Navigator.getInstance().getPage().onKeypad(message.getString("key"));
                            }

                            if (message.getString("type").equals("rfid_read")) {
                                Navigator.getInstance().getPage().onRFIDRead(message.getString("account_id"), message.getString("rfid_uid"));
                            }

                            if (message.getString("type").equals("rfid_write")) {
                                Navigator.getInstance().getPage().onRFIDWrite(message.getString("account_id"), message.getString("rfid_uid"));
                            }
                        }
                    });
                } catch (Exception e) {
                    e.printStackTrace();
                }
            }
        }
    }

    public int getWindowWidth() {
        return frame.getWidth();
    }

    public int getWindowHeight() {
        return frame.getHeight();
    }

    public void repaintWindow() {
        frame.getContentPane().validate();
        frame.getContentPane().repaint();
    }

    public void sendWriteRFID(String account_id) {
        JSONObject message = new JSONObject();
        message.put("type", "rfid_write");
        message.put("account_id", account_id);
        sendMessage(message);
    }

    public void sendBeeper(int frequency, int duration) {
        JSONObject message = new JSONObject();
        message.put("type", "beeper");
        message.put("frequency", frequency);
        message.put("duration", duration);
        sendMessage(message);
    }

    public void sendPrinter(String[] lines) {
        JSONObject message = new JSONObject();
        message.put("type", "printer");
        message.put("lines", lines);
        sendMessage(message);
    }

    public void sendMessage(JSONObject message) {
        String line = message.toString() + "\n";
        byte[] bytes = line.getBytes();
        serialPort.writeBytes(bytes, bytes.length);
        System.out.print("[INFO] Write: " + line);
    }
}
