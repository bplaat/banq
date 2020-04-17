package ml.banq.atm;

import java.awt.Component;
import java.awt.Dimension;
import java.awt.FlowLayout;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JLabel;
import javax.swing.JPanel;
import javax.swing.JTextField;

// The admin location page
public class AdminLocationPage extends Page {
    private static final long serialVersionUID = 1;

    private JTextField locationInput;

    public AdminLocationPage() {
        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        add(Box.createVerticalGlue());

        // Create the page title
        JLabel titleLabel = new JLabel(Language.getString("admin_location_page_title"));
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the location input box
        JPanel locationBox = new JPanel(new FlowLayout(FlowLayout.CENTER, Paddings.NORMAL, 0));
        locationBox.setMaximumSize(new Dimension(App.getInstance().getWindowWidth() / 2, 0));
        add(locationBox);

        // Create the location input label
        JLabel locationLabel = new JLabel(Language.getString("admin_location_page_location_input"));
        locationLabel.setFont(Fonts.NORMAL);
        locationBox.add(locationLabel);

        // Create the pincode input field
        locationInput = new JTextField(16);
        locationInput.setText(Settings.getInstance().getItem("location", Config.DEFAULT_LOCATION));
        locationInput.setFont(Fonts.NORMAL);
        locationInput.setHorizontalAlignment(JTextField.CENTER);
        locationBox.add(locationInput);

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the back menu option
        JLabel backLabel = new JLabel("D. " + Language.getString("admin_location_page_back"));
        backLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        backLabel.setFont(Fonts.NORMAL);
        add(backLabel);

        add(Box.createVerticalGlue());
    }

    public void onKeypad(String key) {
        // Save the location
        Settings.getInstance().setItem("location", locationInput.getText());

        // When pressed go back to the previous page
        if (key.equals("D")) {
            Navigator.getInstance().changePage(new AdminMenuPage());
        }
    }
}
