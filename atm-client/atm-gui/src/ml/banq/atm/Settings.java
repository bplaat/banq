package ml.banq.atm;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileReader;
import java.io.FileWriter;
import org.json.JSONObject;

public class Settings {
    private static Settings instance = new Settings();

    public static Settings getInstance() {
        return instance;
    }

    private JSONObject settings;

    private Settings() {
        File settingsFile = new File("settings.json");
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
            } catch (Exception exception) {
                Log.error(exception);
                settings = new JSONObject();
            }
        } else {
            settings = new JSONObject();
        }
    }

    private void saveSettings() {
        try {
            FileWriter settingsFileWriter = new FileWriter("settings.json");
            settingsFileWriter.write(settings.toString());
            settingsFileWriter.close();
        } catch (Exception exception) {
            Log.error(exception);
        }
    }

    public String getItem(String key, String defaultValue) {
        if (settings.has(key)) {
            return settings.getString(key);
        } else {
            return defaultValue;
        }
    }

    public void setItem(String key, String value) {
        settings.put(key, value);

        saveSettings();
    }
}
