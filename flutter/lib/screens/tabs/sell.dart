import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter/rendering.dart';
import 'package:resold/widgets/image-uploader.dart';
import 'package:resold/widgets/scroll-column-expandable.dart';
import 'package:resold/widgets/scrollable-category-list.dart';
import 'package:resold/services/resold-rest.dart';
import 'package:resold/models/product.dart';
import 'package:resold/view-models/response/customer-response.dart';
import 'package:geolocator/geolocator.dart';

class SellPage extends StatefulWidget {
  final CustomerResponse customer;
  final Position currentLocation;

  SellPage(CustomerResponse customer, Position currentLocation, {Key key}) : customer = customer, currentLocation = currentLocation, super(key: key);

  @override
  SellPageState createState() => SellPageState(customer, currentLocation);
}

class SellPageState extends State<SellPage> {

  final CustomerResponse customer;
  final Position currentLocation;
  final List<bool> localGlobalSelected = [false, false];

  String condition;

  // field controllers
  final TextEditingController nameController = TextEditingController();
  final TextEditingController priceController = TextEditingController();
  final TextEditingController detailsController = TextEditingController();

  SellPageState(CustomerResponse customer, Position currentLocation) : customer = customer, currentLocation = currentLocation;

  @override
  Widget build(BuildContext context) {
    var imageUploader = ImageUploader();
    var scrollAbleCategoryList = ScrollableCategoryList();
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
          scrollAbleCategoryList,
          SizedBox(height: 20),
          Row (
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Container (
                  width: 100,
                  child: TextField(
                    controller: priceController,
                    keyboardType: TextInputType.number,
                    decoration: InputDecoration(
                        border: OutlineInputBorder(),
                        labelText: 'Price (\$)'
                    ),
                  )
              ),
              ToggleButtons(
                children: [
                  Padding(padding: EdgeInsets.fromLTRB(20, 8, 20, 8), child: Column (
                      children: [
                        Icon(Icons.local_shipping, semanticLabel: 'Delivery'),
                        Text('Delivery')
                      ],
                    )
                  ),
                  Padding(padding: EdgeInsets.fromLTRB(20, 8, 20, 8), child: Column (
                      children: [
                        Icon(Icons.location_on, semanticLabel: 'Pickup'),
                        Text('Pickup')
                      ]
                    )
                  ),
                ],
                onPressed: (int index) {
                  setState(() {
                    localGlobalSelected[index] = !localGlobalSelected[index];
                  });
                },
                isSelected: localGlobalSelected,
              ),
            ]
          ),
          SizedBox(height: 20),
          Container (
            width: 600,
            child: DropdownButtonFormField<String>(
              value: condition,
              iconSize: 0,
              elevation: 16,
              style: TextStyle(color: Colors.black),
              focusColor: const Color(0xff41b8ea),
              hint: Text('Condition'),
              onChanged: (String newValue) {
                FocusScope.of(context).requestFocus(FocusNode());
                setState(() {
                  condition = newValue;
                });
              },
              decoration: const InputDecoration(
                border: const OutlineInputBorder(),
              ),
              items: <String>['New', 'Like New', 'Good', 'Used'].map<DropdownMenuItem<String>>((String value) {
                return DropdownMenuItem<String>(
                  value: value,
                  child: Text(value),
                );
              }).toList(),
            ),
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
                    return Center(child: CircularProgressIndicator(backgroundColor: const Color(0xff41b8ea)));
                  }
                );
                var product = Product(
                  name: nameController.text,
                  price: priceController.text,
                  description: detailsController.text,
                  condition: getConditionValue(condition).toString(),
                  localGlobal: getLocalGlobalValue(),
                  categoryIds: [getSelectedCategory(scrollAbleCategoryList.state.categorySelected)],
                  latitude: currentLocation.latitude,
                  longitude: currentLocation.longitude
                );
                await ResoldRest.postProduct(customer.token, product, imageUploader.state.imagePaths);
                Navigator.of(context, rootNavigator: true).pop('dialog');
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

  int getConditionValue(String text) {
    switch(text) {
      case 'New':
        return 235;
      case 'Like New':
        return 236;
      case 'Good':
        return 237;
      case 'Used':
        return 238;
      default:
        return 235;
    }
  }

  String getLocalGlobalValue() {
    var localGlobal = '';
    if(localGlobalSelected[0]) {
      localGlobal += '232';
      if(localGlobalSelected[1]) {
        localGlobal += ',';
      }
    }
    if(localGlobalSelected[1]) {
      localGlobal += '231';
    }
    return localGlobal;
  }

  int getSelectedCategory(List<bool> categorySelected) {
    int selectedIndex = 0;
    for(var i = 0; i < categorySelected.length; i++) {
      var selected = categorySelected[i];
      if(selected) {
        selectedIndex = i;
      }
    }
    return getSelectedCategoryId(selectedIndex);
  }

  int getSelectedCategoryId(int index) {
    switch(index) {
      case 0:
        // Electronics
        return 42;
      case 1:
        // Fashion
        return 93;
      case 2:
        // Home and Lawn
        return 100;
      case 3:
        // Outdoors
        return 101;
      case 4:
        // Sporting goods
        return 102;
      case 5:
        // Music
        return 103;
      case 6:
        // Collectibles
        return 104;
      case 7:
        // Handmade
        return 105;
      default:
        // Electronics
        return 42;
    }
  }
}
