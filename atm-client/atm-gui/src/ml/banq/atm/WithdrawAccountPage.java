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
        BanqAPI.Account account = BanqAPI.getActiveAccount();

        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        add(Box.createVerticalGlue());

        JLabel titleLabel = new JLabel("Acount name: " + account.getName());
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);

        add(Box.createVerticalStrut(16));

        JLabel amountLabel = new JLabel(String.format("\u20ac %.2f", account.getAmount()));
        amountLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        amountLabel.setFont(Fonts.NORMAL);
        add(amountLabel);

        add(Box.createVerticalStrut(24));

        JLabel menu1Label = new JLabel("1. Withdraw money");
        menu1Label.setAlignmentX(Component.CENTER_ALIGNMENT);
        menu1Label.setFont(Fonts.NORMAL);
        add(menu1Label);

        add(Box.createVerticalStrut(16));

        JLabel menu2Label = new JLabel("D. Logout and go back to the welcome page");
        menu2Label.setAlignmentX(Component.CENTER_ALIGNMENT);
        menu2Label.setFont(Fonts.NORMAL);
        add(menu2Label);

        add(Box.createVerticalGlue());
    }

    public void onKeypad(String key) {
        if (key.equals("1")) {
            Navigator.changePage(new WithdrawAmountPage());
        }

        if (key.equals("D")) {
            Navigator.changePage(new WelcomePage());
        }
    }
}
