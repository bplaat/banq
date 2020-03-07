mkdir classes
if javac -Xlint -cp "jSerialComm-2.6.0.jar;json-20190722.jar" -d classes $(find src -name *.java); then
    jar cfm atm.jar src/manifest.mf -C classes .
    java -jar atm.jar
fi
rm -r classes
