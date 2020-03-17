package ml.banq.atm;

import java.awt.Component;
import java.text.SimpleDateFormat;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JLabel;

public class WithdrawReceiptPage extends Page {
    private static final long serialVersionUID = 1;

    private BanqAPI.Transaction transaction;

    public WithdrawReceiptPage(BanqAPI.Transaction transaction) {
        this.transaction = transaction;

        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        add(Box.createVerticalGlue());

        JLabel titleLabel = new JLabel("The withdraw is accepted");
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        JLabel messageLabel = new JLabel("Do you want a receipt?");
        messageLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        messageLabel.setFont(Fonts.NORMAL);
        add(messageLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        JLabel menu1Label = new JLabel("1. Yes");
        menu1Label.setAlignmentX(Component.CENTER_ALIGNMENT);
        menu1Label.setFont(Fonts.NORMAL);
        add(menu1Label);

        add(Box.createVerticalStrut(Paddings.NORMAL));

        JLabel menu2Label = new JLabel("2. No");
        menu2Label.setAlignmentX(Component.CENTER_ALIGNMENT);
        menu2Label.setFont(Fonts.NORMAL);
        add(menu2Label);

        add(Box.createVerticalGlue());
    }

    public void onKeypad(String key) {
        if (key.equals("1")) {
            App.getInstance().sendBeeper(880, 250);

            App.getInstance().sendPrinter(new String[] {
                Utils.printerHorizontalLine(),
                "",
                Utils.printerCenter("WITHDRAW DETAILS"),
                "",
                Utils.printerPad("Bank name:", Config.BANK_NAME),
                Utils.printerPad("Account number:", transaction.getFromAccountId()),
                Utils.printerPad("Transaction number:", String.format("%08d", transaction.getId())),
                Utils.printerPad("Amount:", String.format("$ %.02f", transaction.getAmount())),
                Utils.printerPad("Location:", Config.DEVICE_LOCATION),
                Utils.printerPad("Time:", new SimpleDateFormat("yyyy-MM-dd HH:mm:ss").format(transaction.getCreatedAt())),
                "",
                Utils.printerHorizontalLine(),
                "",
                ""
            });

            Navigator.getInstance().changePage(new WithdrawDonePage());
        }

        if (key.equals("2")) {
            App.getInstance().sendBeeper(880, 250);

            Navigator.getInstance().changePage(new WithdrawDonePage());
        }
    }
}
