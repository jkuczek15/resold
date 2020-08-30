import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter/rendering.dart';
import 'package:resold/widgets/image/image-uploader.dart';
import 'package:resold/widgets/scroll/scroll-column-expandable.dart';
import 'package:resold/widgets/dropdown/dropdown-category-list.dart';
import 'package:resold/services/resold-rest.dart';
import 'package:resold/models/product.dart';
import 'package:resold/view-models/response/customer-response.dart';
import 'package:resold/screens/product/view.dart';
import 'package:geolocator/geolocator.dart';
import 'package:resold/widgets/dropdown/dropdown-size-list.dart';
import 'package:resold/widgets/dropdown/dropdown-condition-list.dart';
import 'package:resold/widgets/loading.dart';

class SellPage extends StatefulWidget {
  final CustomerResponse customer;

  SellPage(CustomerResponse customer, {Key key}) : customer = customer, super(key: key);

  @override
  SellPageState createState() => SellPageState(customer);
}

class SellPageState extends State<SellPage> {

  final CustomerResponse customer;
  Position currentLocation;
  String condition;

  // widget keys
  final imageUploaderKey = new GlobalKey<ImageUploaderState>();
  final dropdownCategoryKey = new GlobalKey<DropdownCategoryListState>();
  final dropdownSizeKey = new GlobalKey<DropdownSizeListState>();
  final dropdownConditionKey = new GlobalKey<DropdownConditionListState>();

  // field controllers
  final TextEditingController nameController = TextEditingController();
  final TextEditingController priceController = TextEditingController();
  final TextEditingController detailsController = TextEditingController();

  SellPageState(CustomerResponse customer) : customer = customer;

  @override
  void initState() {
    super.initState();
    Geolocator().getCurrentPosition(desiredAccuracy: LocationAccuracy.high).then((location) {
      if(this.mounted) {
        setState(() {
          currentLocation = location;
        });
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    final imageUploader = ImageUploader(key: imageUploaderKey);
    final dropdownCategoryList = DropdownCategoryList(key: dropdownCategoryKey);
    final dropdownSizeList = DropdownSizeList(key: dropdownSizeKey);
    final dropdownConditionList = DropdownConditionList(key: dropdownConditionKey);
    return Padding (
      padding: EdgeInsets.all(20),
      child: ScrollColumnExpandable(
        mainAxisAlignment: MainAxisAlignment.start,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          imageUploader,
          SizedBox(height: 20),
          TextField(
            controller: nameController,
            decoration: InputDecoration(
              border: OutlineInputBorder(),
              labelText: 'Listing Title'
            ),
          ),
          SizedBox(height: 20),
          dropdownCategoryList,
          SizedBox(height: 20),
          dropdownSizeList,
          SizedBox(height: 20),
          Row (
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Container (
                  width: 125,
                  height: 60,
                  child: TextField(
                    controller: priceController,
                    keyboardType: TextInputType.number,
                    decoration: InputDecoration(
                        border: OutlineInputBorder(),
                        labelText: 'Price (\$)'
                    ),
                  )
              ),
              Container (
                width: 150,
                height: 60,
                child: dropdownConditionList
              ),
            ]
          ),
          SizedBox(height: 20),
          TextField(
            controller: detailsController,
            maxLines: null,
            minLines: null,
            keyboardType: TextInputType.multiline,
            decoration: InputDecoration(
              border: OutlineInputBorder(),
              labelText: 'Details',
            ),
          ),
          SizedBox(height: 20),
          ButtonTheme (
            minWidth: 340.0,
            height: 70.0,
            child: RaisedButton(
              shape: RoundedRectangleBorder(
                  borderRadius: BorderRadiusDirectional.circular(8)
              ),
              onPressed: () async {
                // show a loading indicator
                showDialog(
                  context: context,
                  builder: (BuildContext context) {
                    return Center(child: Loading());
                  }
                );
                var product = Product(
                  name: nameController.text,
                  price: priceController.text,
                  description: detailsController.text,
                  condition: dropdownConditionKey.currentState.conditionSelected,
                  categoryIds: [int.tryParse(dropdownCategoryKey.currentState.categorySelected)],
                  itemSize: int.tryParse(dropdownSizeKey.currentState.sizeSelected),
                  latitude: currentLocation.latitude,
                  longitude: currentLocation.longitude,
                  localGlobal: '231,232'
                );
                var response = await ResoldRest.postProduct(customer.token, product, imageUploaderKey.currentState.imagePaths);
                product.id = int.tryParse(response);
                Navigator.of(context, rootNavigator: true).pop('dialog');
                Navigator.push(context, MaterialPageRoute(builder: (context) => ProductPage(product, customer, currentLocation)));
              },
              child: Text('Post',
                style: new TextStyle(
                  fontSize: 20.0,
                  fontWeight: FontWeight.bold,
                  color: Colors.white
                )
              ),
              color: Colors.black,
              textColor: Colors.white,
            )
        )
      ]
      )
    );
  }
}
