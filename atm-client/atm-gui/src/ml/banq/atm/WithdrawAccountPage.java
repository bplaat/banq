package ml.banq.atm;

import java.awt.Component;
import java.awt.Font;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JLabel;
import javax.swing.JPanel;

public class WithdrawAccountPage extends Page {
    private static final long serialVersionUID = 1;

    public WithdrawAccountPage() {
        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        add(Box.createVerticalGlue());

        JLabel titleLabel = new JLabel("Acount name: dajksfhdkdjsahkhfad");
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);

        add(Box.createVerticalStrut(24));

        JLabel amountLabel = new JLabel("Amount: \u20ac 45,65");
        amountLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        amountLabel.setFont(Fonts.NORMAL);
        add(amountLabel);

        add(Box.createVerticalStrut(48));

        JLabel menuLabel = new JLabel("1. Withdraw money");
        menuLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        menuLabel.setFont(Fonts.NORMAL);
        add(menuLabel);

        add(Box.createVerticalGlue());
    }

    public void onKeypad(String key) {
        if (key.equals("1")) {
            Navigator.changePage(new WithdrawAmountPage());
        }
    }
}
