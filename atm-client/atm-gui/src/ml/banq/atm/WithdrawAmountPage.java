package ml.banq.atm;

import java.awt.Component;
import java.text.SimpleDateFormat;
import java.util.Date;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JLabel;

public class WithdrawAmountPage extends Page {
    private static final long serialVersionUID = 1;

    private String accountId;
    private String rfid_uid;
    private String pincode;
    private BanqAPI.Account account;

    private JLabel messageLabel;

    public WithdrawAmountPage(String accountId, String rfid_uid, String pincode, BanqAPI.Account account) {
        this.accountId = accountId;
        this.rfid_uid = rfid_uid;
        this.pincode = pincode;
        this.account = account;

        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        add(Box.createVerticalGlue());

        JLabel titleLabel = new JLabel(Language.getString("withdraw_amount_page_title"));
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);
        add(Box.createVerticalStrut(Paddings.LARGE));

        messageLabel = new JLabel(Language.getString("withdraw_amount_page_message"));
        messageLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        messageLabel.setFont(Fonts.NORMAL);
        add(messageLabel);

        add(Box.createVerticalStrut(Paddings.NORMAL));

        for (int i = 0; i < Config.DEFAULT_AMOUNTS.length; i++) {
            JLabel amountLabel = new JLabel((i + 1) + ". \u20ac " + Config.DEFAULT_AMOUNTS[i]);
            amountLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
            amountLabel.setFont(Fonts.NORMAL);
            add(amountLabel);
            add(Box.createVerticalStrut(Paddings.NORMAL));
        }

        JLabel customLabel = new JLabel((Config.DEFAULT_AMOUNTS.length + 1) + ". " + Language.getString("withdraw_amount_page_custom"));
        customLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        customLabel.setFont(Fonts.NORMAL);
        add(customLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        JLabel backLabel = new JLabel("D. " + Language.getString("withdraw_amount_page_back"));
        backLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        backLabel.setFont(Fonts.NORMAL);
        add(backLabel);

        add(Box.createVerticalGlue());
    }

    public void onKeypad(String key) {
        if (key.equals("D")) {
            Navigator.getInstance().changePage(new WithdrawAccountPage(accountId, rfid_uid, pincode, account));
        }

        for (int i = 0; i < Config.DEFAULT_AMOUNTS.length; i++) {
            if (key.equals(String.valueOf(i + 1))) {
                float amount = Config.DEFAULT_AMOUNTS[i];

                if (account.getAmount() - Config.DEFAULT_AMOUNTS[i] >= 0) {
                    String name = Language.getString("withdraw_amount_page_transaction_prefix") + " " + new SimpleDateFormat("yyyy-MM-dd HH:mm:ss").format(new Date());
                    BanqAPI.Transaction transaction = BanqAPI.getInstance().createTransaction(accountId, rfid_uid, pincode, name, "SU-BANQ-00000001", amount);
                    if (transaction != null) {
                        Navigator.getInstance().changePage(new WithdrawReceiptPage(transaction));
                    } else {
                        App.getInstance().sendBeeper(110, 250);
                        messageLabel.setText(Language.getString("withdraw_amount_page_error"));
                    }
                } else {
                    App.getInstance().sendBeeper(110, 250);
                    messageLabel.setText(Language.getString("withdraw_amount_page_not_enough"));
                }
            }
        }

        if (key.equals(String.valueOf(Config.DEFAULT_AMOUNTS.length + 1))) {
            Navigator.getInstance().changePage(new WithdrawCustomAmountPage(accountId, rfid_uid, pincode, account));
        }
    }
}
