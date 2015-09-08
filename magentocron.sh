date_script_start=$(date +"%s")
clear
printf "\n"
echo "-----------------------------------------------------------"
echo "Started magentocron.sh (ByPBC) !"
date -R
echo "------------------------"
printf "\n"

echo "Starting the CSV conversion..."
printf "\n"
php /var/www/html/magentostudy/var/import/parse/pbcmagmi.php
# php /var/www/html/magentostudy/var/import/parse/csvgen20000.php
printf "\n\n"
date_conversion_finished=$(date +"%s")
difference_conversion=$(($date_conversion_finished-$date_script_start))
echo "$(($difference_conversion / 60)) minutes and $(($difference_conversion % 60)) seconds elapsed @ Conversion"

echo "------------------------"
printf "\n"
echo "Starting Magmi Import..."
date_magmi_import_start=$(date +"%s")
#This assumes we have a profile outputcsv saved in magmi , that opens the outputfile.csv!
php /var/www/html/magentostudy/magimprt/cli/magmi.cli.php -profile="outputcsv" -mode="create"
date_magmi_import_end=$(date +"%s")

diff_magmi_import=$(($date_magmi_import_end-$date_magmi_import_start))
printf "\n"
echo "$(($diff_magmi_import / 60)) minutes and $(($diff_magmi_import % 60)) seconds elapsed while importing."


echo "------------------------"
printf "\n"
echo "Starting to reindex all magento."
printf "\n"
date_reindex_start=$(date +"%s")
php /var/www/html/magentostudy/shell/indexer.php reindexall

date_reindex_end=$(date +"%s")
printf "\n"

diff_magento_reindex=$(($date_reindex_end-$date_reindex_start))
echo "$(($diff_magento_reindex / 60)) minutes and $(($diff_magento_reindex % 60)) seconds elapsed while reindexing."


date_script_end=$(date +"%s")

diff=$(($date_script_end-$date_script_start))
echo "------------------------"
printf "\n"
echo "Full script time :"

echo "$(($diff / 60)) minutes and $(($diff % 60)) seconds."
printf "\n"
echo "-----------------------------------------------------------"
