package ml.banq.atm;

import java.awt.event.ActionEvent;
import java.awt.Component;
import java.awt.Dimension;
import java.awt.FlowLayout;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JButton;
import javax.swing.JLabel;
import javax.swing.JPanel;
import javax.swing.JTextField;

// The admin write foreign input page
public class AdminWriteForeignInputPage extends Page {
    private static final long serialVersionUID = 1;

    private JLabel messageLabel;
    private JTextField accountInput;

    public AdminWriteForeignInputPage() {
        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        add(Box.createVerticalGlue());

        // Create the page title
        JLabel titleLabel = new JLabel(Language.getString("admin_write_foreign_input_page_title"));
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the page message label
        messageLabel = new JLabel(Language.getString("admin_write_foreign_input_page_message"));
        messageLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        messageLabel.setFont(Fonts.NORMAL);
        add(messageLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the page account input label
        JLabel accountLabel = new JLabel(Language.getString("admin_write_foreign_input_page_account_input"));
        accountLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        accountLabel.setFont(Fonts.NORMAL);
        add(accountLabel);

        add(Box.createVerticalStrut(Paddings.NORMAL));

        // Create the page account input field
        accountInput = new JTextField(16);
        accountInput.setFont(Fonts.NORMAL);
        accountInput.setHorizontalAlignment(JTextField.CENTER);
        accountInput.setMaximumSize(accountInput.getPreferredSize());
        add(accountInput);

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the buttons box
        JPanel buttonsBox = new JPanel(new FlowLayout(FlowLayout.CENTER, Paddings.NORMAL, 0));
        buttonsBox.setMaximumSize(new Dimension(App.getInstance().getWindowWidth() / 2, 0));
        add(buttonsBox);

        // Create the write button
        JButton writeButton = new JButton(Language.getString("admin_write_foreign_input_page_write_button"));
        writeButton.setFont(Fonts.NORMAL);
        writeButton.addActionListener((ActionEvent event) -> {
            tryToWrite();
        });
        buttonsBox.add(writeButton);

        // Create the back button
        JButton backButton = new JButton(Language.getString("admin_write_foreign_input_page_back_button"));
        backButton.setAlignmentX(Component.CENTER_ALIGNMENT);
        backButton.setFont(Fonts.NORMAL);
        backButton.addActionListener((ActionEvent event) -> {
            Navigator.getInstance().changePage(new AdminMenuPage());
        });
        buttonsBox.add(backButton);

        add(Box.createVerticalGlue());
    }

    // Try to go to the next page
    private void tryToWrite() {
        if (accountInput.getText().length() == Config.ACCOUNT_STRING_LENGTH) {
            Navigator.getInstance().changePage(new AdminWriteForeignRFIDPage(accountInput.getText()));
        } else {
            App.getInstance().sendBeeper(110, 250);
            messageLabel.setFont(Fonts.NORMAL_BOLD);
            messageLabel.setText(Language.getString("admin_write_foreign_input_page_error"));
        }
    }

    public void onKeypad(String key) {
        // When pressed try to write
        if (key.equals("#")) {
            tryToWrite();
        }

        // When pressed go back to the previous page
        if (key.equals("D")) {
            Navigator.getInstance().changePage(new AdminMenuPage());
        }
    }
}
