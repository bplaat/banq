package ml.banq.atm;

import java.awt.Component;
import java.text.SimpleDateFormat;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JLabel;

// The withdraw printing page
public class WithdrawPrintingPage extends Page {
    private static final long serialVersionUID = 1;

    public WithdrawPrintingPage(BanqAPI.Transaction transaction) {
        // Send printer commands
        App.getInstance().sendPrinter(new String[] {
            PrinterUtils.horizontalLine(),
            "",
            PrinterUtils.center(Language.getString("withdraw_receipt_title")),
            "",
            PrinterUtils.pad(Language.getString("withdraw_receipt_bank_name"), Config.BANK_NAME),
            PrinterUtils.pad(Language.getString("withdraw_receipt_account_name"), transaction.getFromAccountId()),
            PrinterUtils.pad(Language.getString("withdraw_receipt_transaction_number"), String.format("%08d", transaction.getId())),
            PrinterUtils.pad(Language.getString("withdraw_receipt_amount"), String.format("P %.02f", transaction.getAmount())),
            PrinterUtils.pad(Language.getString("withdraw_receipt_location"), Settings.getInstance().getItem("location", Config.DEFAULT_LOCATION)),
            PrinterUtils.pad(Language.getString("withdraw_receipt_time"), new SimpleDateFormat("yyyy-MM-dd HH:mm:ss").format(transaction.getCreatedAt())),
            "",
            PrinterUtils.horizontalLine(),
            "",
            ""
        });

        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        add(Box.createVerticalGlue());

        // Create the page title
        JLabel titleLabel = new JLabel(Language.getString("withdraw_printing_page_title"));
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the page message label
        JLabel messageLabel = new JLabel(Language.getString("withdraw_printing_page_message"));
        messageLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        messageLabel.setFont(Fonts.NORMAL);
        add(messageLabel);

        add(Box.createVerticalGlue());
    }

    public void onPrinter() {
        // When the printing is finished go to the done page
        Navigator.getInstance().changePage(new WithdrawDonePage());
    }
}
