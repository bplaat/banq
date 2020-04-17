# Build the 'src' and 'resources' folder to a .jar file and run it
mkdir classes
if javac -Xlint -cp "jSerialComm-2.6.0.jar:json-20190722.jar" -d classes $(find src -name *.java); then
    jar cfm atm.jar src/manifest.mf -C classes . resources
    rm -r classes
    java -jar atm.jar
else
    rm -r classes
fi
