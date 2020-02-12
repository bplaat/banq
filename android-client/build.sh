# keytool -genkey -validity 10000 -keystore key.keystore -keyalg RSA -keysize 2048 -storepass ix8Z5mz7 -keypass ix8Z5mz7
PATH=$PATH:~/android-sdk/build-tools/29.0.2:~/android-sdk/platform-tools
PLATFORM=~/android-sdk/platforms/android-29/android.jar
if aapt package -m -J src -M AndroidManifest.xml -S res -I $PLATFORM; then
    mkdir classes
    if javac -Xlint -cp $PLATFORM -d classes src/ml/banq/android/*.java; then
        dx.bat --dex --output=classes.dex classes
        aapt package -F banq-unaligned.apk -M AndroidManifest.xml -S res -I $PLATFORM
        aapt add banq-unaligned.apk classes.dex
        zipalign -f -p 4 banq-unaligned.apk banq.apk
        rm -r classes src/ml/banq/android/R.java classes.dex banq-unaligned.apk
        apksigner.bat sign --ks key.keystore --ks-pass pass:ix8Z5mz7 --ks-pass pass:ix8Z5mz7 banq.apk
        adb install -r banq.apk
        adb shell am start -n ml.banq.android/.MainActivity
    else
        rm -r classes src/ml/banq/android/R.java
    fi
fi
