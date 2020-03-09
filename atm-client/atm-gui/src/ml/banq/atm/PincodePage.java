package ml.banq.atm;

import java.awt.Component;
import java.awt.Font;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JLabel;
import javax.swing.JPanel;

public class PincodePage extends Page {
    private static final long serialVersionUID = 1;

    private String pincode = "";
    private JLabel pincodeLabel;

    public PincodePage() {
        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        add(Box.createVerticalGlue());

        JLabel titleLabel = new JLabel("Enter your pincode");
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);

        add(Box.createVerticalStrut(24));

        pincodeLabel = new JLabel("Pincode: ");
        pincodeLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        pincodeLabel.setFont(Fonts.NORMAL);
        add(pincodeLabel);

        add(Box.createVerticalGlue());
    }

    public void onKeypad(String key) {
        if (key.matches("[0-9]") && pincode.length() < 4) {
            pincode += key;
        }

        if (key.equals("*")) {
            pincode = "";
        }

        String pincodeString = "Pincode: ";
        for (int i = 0; i < pincode.length(); i++) pincodeString += "*";
        pincodeLabel.setText(pincodeString);

        if (pincode.length() == 4 && key.equals("#")) {
            App.getInstance().sendBeeper(440, 250);

            try {
                Thread.sleep(350);
            } catch (Exception e) {
                e.printStackTrace();
            }

            App.getInstance().sendBeeper(440, 250);

            BanqAPI.setPincode(pincode);
            Navigator.changePage(new AccountPage());
        }
    }
}
