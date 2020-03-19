package ml.banq.atm;

import javax.swing.JPanel;

// The abstract page class
abstract public class Page extends JPanel {
    private static final long serialVersionUID = 1;

    public void onKeypad(String key) {}

    public void onRFIDRead(String account_id, String rfid_uid) {}

    public void onRFIDWrite(String account_id, String rfid_uid) {}
}
