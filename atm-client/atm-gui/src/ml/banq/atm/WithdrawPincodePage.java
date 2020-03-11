package ml.banq.atm;

import java.awt.Component;
import java.awt.Dimension;
import java.awt.FlowLayout;
import java.awt.Font;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JLabel;
import javax.swing.JPanel;
import javax.swing.JPasswordField;

public class WithdrawPincodePage extends Page {
    private static final long serialVersionUID = 1;

    private JLabel messageLabel;
    private JPasswordField pincodeInput;

    public WithdrawPincodePage() {
        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        add(Box.createVerticalGlue());

        JLabel titleLabel = new JLabel("Enter your pincode");
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);

        add(Box.createVerticalStrut(24));

        messageLabel = new JLabel("Enter your pincode press '#' when you are finished");
        messageLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        messageLabel.setFont(Fonts.NORMAL);
        add(messageLabel);

        add(Box.createVerticalStrut(24));

        JPanel pincodeBox = new JPanel(new FlowLayout(FlowLayout.CENTER, 16, 0));
        pincodeBox.setMaximumSize(new Dimension(320, 64));
        add(pincodeBox);

        JLabel pincodeLabel = new JLabel("Pincode: ");
        pincodeLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        pincodeLabel.setFont(Fonts.NORMAL);
        pincodeBox.add(pincodeLabel);

        pincodeInput = new JPasswordField(4);
        pincodeInput.setFont(Fonts.NORMAL);
        pincodeInput.setHorizontalAlignment(JPasswordField.CENTER);
        pincodeInput.setMaximumSize(pincodeInput.getPreferredSize());
        pincodeBox.add(pincodeInput);

        add(Box.createVerticalStrut(24));

        JLabel backLabel = new JLabel("Press the 'D' key to go back to the welcome page");
        backLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        backLabel.setFont(Fonts.NORMAL);
        add(backLabel);

        add(Box.createVerticalGlue());
    }

    public void onKeypad(String key) {
        if (key.equals("D")) {
            Navigator.changePage(new WelcomePage());
        }

        String pincode = new String(pincodeInput.getPassword());

        if (key.matches("[0-9]") && pincode.length() < 4) {
            pincodeInput.setText(pincode + key);
        }

        if (key.equals("*")) {
            pincodeInput.setText("");
        }

        if (pincode.length() == 4 && key.equals("#")) {
            BanqAPI.setPincode(pincode);
            String message = BanqAPI.loadActiveAccount();
            if (message.equals("success")) {
                App.sendBeeper(880, 250);
                Navigator.changePage(new WithdrawAccountPage());
            } else {
                App.sendBeeper(110, 250);
                messageLabel.setText("Error: " + message);
                pincodeInput.setText("");
            }
        }
    }
}
