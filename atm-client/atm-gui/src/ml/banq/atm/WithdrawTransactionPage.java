package ml.banq.atm;

import java.awt.Component;
import java.awt.Font;
import java.text.SimpleDateFormat;
import java.util.Date;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JLabel;
import javax.swing.JPanel;

public class WithdrawTransactionPage extends Page {
    private static final long serialVersionUID = 1;

    public WithdrawTransactionPage() {
        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        add(Box.createVerticalGlue());

        JLabel titleLabel = new JLabel("The transaction is successfull");
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);

        add(Box.createVerticalStrut(24));

        JLabel messageLabel = new JLabel("Do you want a receid?");
        messageLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        messageLabel.setFont(Fonts.NORMAL);
        add(messageLabel);

        add(Box.createVerticalStrut(24));

        JLabel menu1Label = new JLabel("1. Yes");
        menu1Label.setAlignmentX(Component.CENTER_ALIGNMENT);
        menu1Label.setFont(Fonts.NORMAL);
        add(menu1Label);

        add(Box.createVerticalStrut(16));

        JLabel menu2Label = new JLabel("2. No");
        menu2Label.setAlignmentX(Component.CENTER_ALIGNMENT);
        menu2Label.setFont(Fonts.NORMAL);
        add(menu2Label);

        add(Box.createVerticalGlue());
    }

    public void onKeypad(String key) {
        if (key.equals("1")) {
            App.sendPrinter(new String[] {
                Utils.printerHorizontalLine(),
                "",
                Utils.printerCenter("PAYMENT DETAILS"),
                "",
                Utils.printerPad("Bank name:", Config.BANK_NAME),
                Utils.printerPad("Account number:", BanqAPI.getAccountId()),
                Utils.printerPad("Transaction number:", "00000005"),
                Utils.printerPad("Amount:", "$ " + String.valueOf(BanqAPI.getAmount())),
                Utils.printerPad("Location:", Config.ATM_LOCATION),
                Utils.printerPad("Time:", new SimpleDateFormat("yyyy-MM-dd HH:mm:ss").format(new Date())),
                "",
                Utils.printerHorizontalLine(),
                "",
                ""
            });

            Navigator.changePage(new WithdrawDonePage());
        }

        if (key.equals("2")) {
            Navigator.changePage(new WithdrawDonePage());
        }
    }
}
