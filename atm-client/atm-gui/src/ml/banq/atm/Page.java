package ml.banq.atm;

import javax.swing.JPanel;

abstract public class Page extends JPanel {
    private static final long serialVersionUID = 1;

    public void onKeypad(String key) {}

    public void onRFID(String rfid) {}
}
