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
    public static final String ADMIN_RFID_UID = "4a360c0b";

    private static App instance = new App();

    private SerialPort serialPort;

    private JLabel infoLabel;

    private App() {
        BanqAPI.setKey("38cd34142710c0b70419cb36dc2de1ae");
    }

    public static App getInstance() {
        return instance;
    }

    public void run() {
        try {
            UIManager.setLookAndFeel(UIManager.getSystemLookAndFeelClassName());
        } catch (Exception e) {}

        JFrame frame = new JFrame("Banq ATM GUI");
        frame.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        frame.setSize(800, 600);
        frame.setLocationRelativeTo(null);

        frame.add(Navigator.getInstance());
        Navigator.changePage(new WelcomePage());

        SerialPort[] serialPorts = SerialPort.getCommPorts();
        if (serialPorts.length > 0) {
            serialPort = serialPorts[0];
            serialPort.openPort();
            serialPort.addDataListener(this);
        }

        frame.setVisible(true);
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
            System.out.print("Read: " + line);
            if (line.charAt(0) == '{') {
                try {
                    JSONObject message = new JSONObject(line);
                    SwingUtilities.invokeLater(new Runnable() {
                        public void run() {
                            if (message.getString("type").equals("keypad")) {
                                Navigator.getPage().onKeypad(message.getString("key"));
                            }

                            if (message.getString("type").equals("rfid_read")) {
                                sendBeeper(440, 250);
                                Navigator.getPage().onRFIDRead(message.getString("rfid_uid"), message.getString("account_id"));
                            }

                            if (message.getString("type").equals("rfid_write")) {
                                sendBeeper(880, 250);
                                Navigator.getPage().onRFIDWrite(message.getString("rfid_uid"), message.getString("account_id"));
                            }
                        }
                    });
                } catch (Exception e) {
                    e.printStackTrace();
                }
            }
        }
    }

    public static void sendWriteRFID(String account_id) {
        JSONObject message = new JSONObject();
        message.put("type", "rfid_write");
        message.put("account_id", account_id);
        sendMessage(message);
    }

    public static void sendBeeper(int frequency, int duration) {
        JSONObject message = new JSONObject();
        message.put("type", "beeper");
        message.put("frequency", frequency);
        message.put("duration", duration);
        sendMessage(message);
    }

    public static void sendPrinter(String[] lines) {
        JSONObject message = new JSONObject();
        message.put("type", "printer");
        message.put("lines", lines);
        sendMessage(message);
    }

    public static void sendMessage(JSONObject message) {
        String line = message.toString() + "\n";
        byte[] bytes = line.getBytes();
        instance.serialPort.writeBytes(bytes, bytes.length);
        System.out.print("Write: " + line);
    }
}
