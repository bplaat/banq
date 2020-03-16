package ml.banq.atm;

import com.fazecast.jSerialComm.SerialPort;
import com.fazecast.jSerialComm.SerialPortEvent;
import com.fazecast.jSerialComm.SerialPortMessageListener;
import java.awt.Color;
import java.awt.Component;
import java.awt.Dimension;
import java.awt.Font;
import java.awt.GridBagConstraints;
import java.awt.GridBagLayout;
import java.awt.Insets;
import javax.swing.BorderFactory;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JPanel;
import javax.swing.JTabbedPane;
import javax.swing.JScrollPane;
import javax.swing.UIManager;
import javax.swing.SwingConstants;
import javax.swing.SwingUtilities;
import org.json.JSONObject;

public class App implements Runnable, SerialPortMessageListener {
    private static App instance = new App();

    private JFrame frame;
    private SerialPort serialPort;
    private JLabel infoLabel;

    private App() {
        BanqAPI.getInstance().setKey(Config.DEVICE_KEY);
    }

    public static App getInstance() {
        return instance;
    }

    public void run() {
        try {
            UIManager.setLookAndFeel(UIManager.getSystemLookAndFeelClassName());
        } catch (Exception e) {}

        frame = new JFrame("Banq ATM GUI");
        frame.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        if (Config.FULLSCREEN_MODE) {
            frame.setExtendedState(JFrame.MAXIMIZED_BOTH);
            frame.setUndecorated(true);
        } else {
            frame.setSize(1280, 720);
            frame.setLocationRelativeTo(null);
        }
        frame.setResizable(false);
        frame.setVisible(true);

        frame.add(Navigator.getInstance());
        Navigator.getInstance().changePage(new WelcomePage());

        SerialPort[] serialPorts = SerialPort.getCommPorts();
        if (serialPorts.length > 0) {
            serialPort = serialPorts[0];
            serialPort.openPort();
            serialPort.addDataListener(this);
        }
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
                                sendBeeper(440, 250);
                                Navigator.getInstance().getPage().onRFIDRead(message.getString("account_id"), message.getString("rfid_uid"));
                            }

                            if (message.getString("type").equals("rfid_write")) {
                                sendBeeper(880, 250);
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
        instance.serialPort.writeBytes(bytes, bytes.length);
        System.out.print("[INFO] Write: " + line);
    }
}
