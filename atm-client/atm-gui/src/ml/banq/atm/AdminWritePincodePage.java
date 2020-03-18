package ml.banq.atm;

import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.Component;
import java.awt.Dimension;
import java.awt.FlowLayout;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JButton;
import javax.swing.JLabel;
import javax.swing.JPanel;
import javax.swing.JPasswordField;
import javax.swing.JOptionPane;

public class AdminWritePincodePage extends Page {
    private static final long serialVersionUID = 1;

    public AdminWritePincodePage(String accountId) {
        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        add(Box.createVerticalGlue());

        JLabel titleLabel = new JLabel(Language.getString("admin_write_pincode_page_title"));
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        JLabel pincodeLabel = new JLabel(Language.getString("admin_write_pincode_page_pincode_input"));
        pincodeLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        pincodeLabel.setFont(Fonts.NORMAL);
        add(pincodeLabel);

        add(Box.createVerticalStrut(Paddings.NORMAL));

        JPasswordField pincodeInput = new JPasswordField(4);
        pincodeInput.setFont(Fonts.NORMAL);
        pincodeInput.setHorizontalAlignment(JPasswordField.CENTER);
        pincodeInput.setMaximumSize(pincodeInput.getPreferredSize());
        add(pincodeInput);

        add(Box.createVerticalStrut(Paddings.LARGE));

        JLabel pincodeConfirmationLabel = new JLabel(Language.getString("admin_write_pincode_page_pincode_confirmation_input"));
        pincodeConfirmationLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        pincodeConfirmationLabel.setFont(Fonts.NORMAL);
        add(pincodeConfirmationLabel);

        add(Box.createVerticalStrut(Paddings.NORMAL));

        JPasswordField pincodeConfirmationInput = new JPasswordField(4);
        pincodeConfirmationInput.setFont(Fonts.NORMAL);
        pincodeConfirmationInput.setHorizontalAlignment(JPasswordField.CENTER);
        pincodeConfirmationInput.setMaximumSize(pincodeConfirmationInput.getPreferredSize());
        add(pincodeConfirmationInput);

        add(Box.createVerticalStrut(Paddings.LARGE));

        JPanel buttonsBox = new JPanel(new FlowLayout(FlowLayout.CENTER, Paddings.NORMAL, 0));
        buttonsBox.setMaximumSize(new Dimension(App.getInstance().getWindowWidth() / 2, 0));
        add(buttonsBox);

        JButton continueButton = new JButton(Language.getString("admin_write_pincode_page_continue_button"));
        continueButton.setAlignmentX(Component.CENTER_ALIGNMENT);
        continueButton.setFont(Fonts.NORMAL);
        continueButton.addActionListener(new ActionListener() {
            public void actionPerformed(ActionEvent event) {
                String pincode = new String(pincodeInput.getPassword());
                if (pincode.matches("[0-9]{4}")) {
                    if (pincode.equals(new String(pincodeConfirmationInput.getPassword()))) {
                        Navigator.getInstance().changePage(new AdminWriteRFIDPage(accountId, pincode));
                    } else {
                        JOptionPane.showMessageDialog(null, Language.getString("admin_write_pincode_page_error_same"));
                    }
                } else {
                    JOptionPane.showMessageDialog(null, Language.getString("admin_write_pincode_page_error_digits"));
                }
            }
        });
        buttonsBox.add(continueButton);

        JButton backButton = new JButton(Language.getString("admin_write_pincode_page_back_button"));
        backButton.setAlignmentX(Component.CENTER_ALIGNMENT);
        backButton.setFont(Fonts.NORMAL);
        backButton.addActionListener(new ActionListener() {
            public void actionPerformed(ActionEvent event) {
                Navigator.getInstance().changePage(new AdminWriteAccountsPage());
            }
        });
        buttonsBox.add(backButton);

        add(Box.createVerticalGlue());
    }

    public void onKeypad(String key) {
        if (key.equals("D")) {
            Navigator.getInstance().changePage(new AdminWriteAccountsPage());
        }
    }
}
