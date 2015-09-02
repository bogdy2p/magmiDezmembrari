clear
printf "\n"
echo "------------------------"
echo "Started magentocron.sh (ByPBC) !"
date -R
echo "------------------------"
printf "\n"

echo "Starting the conversion"

php /var/www/html/magentostudy/var/import/parse/pbcmagmi.php

echo "conversion done."


#This assumes we have a profile outputcsv saved in magmi , that opens the outputfile.csv!
php /var/www/html/magentostudy/magimprt/cli/magmi.cli.php -profile="outputcsv" -mode="create"
echo "Magmi Import Finished."
printf "\n"
php /var/www/html/magentostudy/shell/indexer.php reindexall
echo "Reindexed all magento."
date -R
printf "\n"

