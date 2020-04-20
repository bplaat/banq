package ml.banq.atm;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileReader;
import java.io.FileWriter;
import org.json.JSONObject;

// The settings persitent storage
public class Settings {
    // The singleton instance
    private static Settings instance = new Settings();

    public static Settings getInstance() {
        return instance;
    }

    // The json hashmap settings storage
    private JSONObject settings;

    private Settings() {
        // Try to read the settings.json file if it exists
        File settingsFile = new File(System.getProperty("user.home") + "/banq-atm-settings.json");
        if (settingsFile.exists() && !settingsFile.isDirectory()) {
            try {
                BufferedReader bufferedReader = new BufferedReader(new FileReader(settingsFile));
                StringBuilder stringBuilder = new StringBuilder();
                String line;
                while ((line = bufferedReader.readLine()) != null) {
                    stringBuilder.append(line);
                    stringBuilder.append(System.lineSeparator());
                }
                bufferedReader.close();

                settings = new JSONObject(stringBuilder.toString());
            }

            // When error with reading the json create an empty settings storage
            catch (Exception exception) {
                Log.warning(exception);
                settings = new JSONObject();
            }
        } else {
            // Create an empty settings storage
            settings = new JSONObject();
        }
    }

    // Saves the json settings to the settings.json file
    public void save() {
        try {
            FileWriter settingsFileWriter = new FileWriter(System.getProperty("user.home") + "/banq-atm-settings.json");
            settingsFileWriter.write(settings.toString());
            settingsFileWriter.close();
        } catch (Exception exception) {
            Log.error(exception);
        }
    }

    // Get an string item form the settings with a defaultValue
    public String getItem(String key, String defaultValue) {
        if (settings.has(key)) {
            return settings.getString(key);
        } else {
            return defaultValue;
        }
    }

    // Set an string item of the settings
    public void setItem(String key, String value) {
        settings.put(key, value);
    }

    // Get an int item form the settings with a defaultValue
    public int getItem(String key, int defaultValue) {
        if (settings.has(key)) {
            return settings.getInt(key);
        } else {
            return defaultValue;
        }
    }

    // Set an int item of the settings
    public void setItem(String key, int value) {
        settings.put(key, value);
    }
}
