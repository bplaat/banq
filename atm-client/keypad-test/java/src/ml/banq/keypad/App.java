package ml.banq.keypad;

// Load the serial libary classes
import com.fazecast.jSerialComm.SerialPort;
import com.fazecast.jSerialComm.SerialPortDataListener;
import com.fazecast.jSerialComm.SerialPortEvent;

// Load the swing & awt classes
import java.awt.Font;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.UIManager;
import javax.swing.SwingConstants;

public class App {
    public App() {
        // Let Java Swing use the Native theme
        try {
            UIManager.setLookAndFeel(UIManager.getSystemLookAndFeelClassName());
        } catch (Exception e) {}

        // Create a centered window
        JFrame frame = new JFrame("Keypad Serial Demo");
        frame.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        frame.setSize(800, 600);
        frame.setLocationRelativeTo(null);

        // Create the large centered info label
        JLabel infoLabel = new JLabel("Connecting...");
        infoLabel.setFont(new Font(infoLabel.getFont().getName(), Font.BOLD, 64));
        infoLabel.setHorizontalAlignment(SwingConstants.CENTER);
        frame.add(infoLabel);

        // Checl if a serial port is available
        SerialPort[] serialPorts = SerialPort.getCommPorts();
        if (serialPorts.length > 0) {
            // Connected to the first serial port and add listeners
            SerialPort comPort = serialPorts[0];
            comPort.openPort();
            comPort.addDataListener(new SerialPortDataListener() {
                @Override
                public int getListeningEvents() {
                    return SerialPort.LISTENING_EVENT_DATA_AVAILABLE;
                }

                @Override
                public void serialEvent(SerialPortEvent event) {
                    // When data is available read it convert it to a string and set the label to it
                    if (event.getEventType() == SerialPort.LISTENING_EVENT_DATA_AVAILABLE) {
                        byte[] bytes = new byte[comPort.bytesAvailable()];
                        comPort.readBytes(bytes, bytes.length);

                        String text = new String(bytes);
                        infoLabel.setText(text);
                    }
                }
            });
        }

        else {
            infoLabel.setText("No serial port");
        }

        // Show the window
        frame.setVisible(true);
    }

    // The main entry point
    public static void main(String[] args) {
        // Run the app
        new App();
    }
}
