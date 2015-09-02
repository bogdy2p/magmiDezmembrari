clear
printf "\n"
echo "------------------------"
echo "Started magentocron.sh (ByPBC) !"
date -R
echo "------------------------"
printf "\n"

echo "Starting the conversion @"
date -R
printf "\n"
php /var/www/html/magentostudy/var/import/parse/pbcmagmi.php
printf "\n\n"

echo "Starting Magmi Import @"
date -R
#This assumes we have a profile outputcsv saved in magmi , that opens the outputfile.csv!
php /var/www/html/magentostudy/magimprt/cli/magmi.cli.php -profile="outputcsv" -mode="create"

printf "\n"
echo "Starting to reindex all magento."
php /var/www/html/magentostudy/shell/indexer.php reindexall
printf "\n"
echo "Reindexed all magento. Good to go !"