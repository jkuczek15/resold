file=$1

if [[ -n "$file" ]]; then
    # Change to the vendor directory
    cd /var/www/html/vendor/

    # Copy the file along with folder structure
    cp --parents $1 /var/www/html/vendor/resold
else
    echo "Please provide a vendor module path as an argument. Ex. magento/module-paypal/Controller/Payflow.php"
fi
