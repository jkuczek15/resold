import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter/rendering.dart';
import 'package:resold/enums/selected-tab.dart';
import 'package:resold/helpers/local-global-helper.dart';
import 'package:resold/services/resold.dart';
import 'package:resold/state/actions/set-for-sale.dart';
import 'package:resold/state/actions/set-selected-tab.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/widgets/image/image-uploader.dart';
import 'package:resold/widgets/scroll/scroll-column-expandable.dart';
import 'package:resold/widgets/dropdown/dropdown-category-list.dart';
import 'package:resold/services/resold-rest.dart';
import 'package:resold/models/product.dart';
import 'package:resold/screens/product/view.dart';
import 'package:geolocator/geolocator.dart';
import 'package:resold/widgets/dropdown/dropdown-size-list.dart';
import 'package:resold/widgets/dropdown/dropdown-condition-list.dart';
import 'package:resold/widgets/loading.dart';

class SellPage extends StatelessWidget {
  final String condition = '';

  // widget keys
  final imageUploaderKey = new GlobalKey<ImageUploaderState>();
  final dropdownCategoryKey = new GlobalKey<DropdownCategoryListState>();
  final dropdownSizeKey = new GlobalKey<DropdownSizeListState>();
  final dropdownConditionKey = new GlobalKey<DropdownConditionListState>();

  // field controllers
  final TextEditingController nameController = TextEditingController();
  final TextEditingController priceController = TextEditingController();
  final TextEditingController detailsController = TextEditingController();
  final formKey = GlobalKey<FormState>();

  final CustomerResponse customer;
  final Position currentLocation;
  final Function dispatcher;

  SellPage({this.customer, this.currentLocation, this.dispatcher});

  @override
  Widget build(BuildContext context) {
    final imageUploader = ImageUploader(key: imageUploaderKey);
    final dropdownCategoryList = DropdownCategoryList(key: dropdownCategoryKey);
    final dropdownSizeList = DropdownSizeList(key: dropdownSizeKey);
    final dropdownConditionList = DropdownConditionList(key: dropdownConditionKey);

    return Padding(
        padding: EdgeInsets.all(20),
        child: ScrollColumnExpandable(
            mainAxisAlignment: MainAxisAlignment.start,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Form(
                  key: formKey,
                  child: Column(children: <Widget>[
                    imageUploader,
                    SizedBox(height: 20),
                    TextFormField(
                      controller: nameController,
                      decoration: InputDecoration(border: OutlineInputBorder(), labelText: 'Listing Title'),
                      validator: (value) {
                        if (value.isEmpty) {
                          return 'Please enter some text.';
                        }
                        return null;
                      },
                    ),
                    SizedBox(height: 20),
                    dropdownCategoryList,
                    SizedBox(height: 20),
                    dropdownSizeList,
                    SizedBox(height: 20),
                    Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
                      Container(
                          width: 125,
                          height: 60,
                          child: TextFormField(
                            controller: priceController,
                            keyboardType: TextInputType.number,
                            decoration: InputDecoration(border: OutlineInputBorder(), labelText: 'Price (\$)'),
                            validator: (value) {
                              if (value.isEmpty) {
                                return 'Please enter a price.';
                              }
                              return null;
                            },
                          )),
                      Container(width: 150, height: 60, child: dropdownConditionList),
                    ]),
                    SizedBox(height: 20),
                    TextFormField(
                      controller: detailsController,
                      maxLines: null,
                      minLines: null,
                      keyboardType: TextInputType.multiline,
                      decoration: InputDecoration(
                        border: OutlineInputBorder(),
                        labelText: 'Details',
                      ),
                      validator: (value) {
                        if (value.isEmpty) {
                          return 'Please enter some text.';
                        }
                        return null;
                      },
                    ),
                    SizedBox(height: 20),
                    ButtonTheme(
                        minWidth: 340.0,
                        height: 70.0,
                        child: RaisedButton(
                          shape: RoundedRectangleBorder(borderRadius: BorderRadiusDirectional.circular(8)),
                          onPressed: () async {
                            if (formKey.currentState.validate()) {
                              // show a loading indicator
                              showDialog(
                                  context: context,
                                  builder: (BuildContext context) {
                                    return Center(child: Loading());
                                  });
                              var product = Product(
                                  name: nameController.text,
                                  price: priceController.text,
                                  description: detailsController.text,
                                  condition: dropdownConditionKey.currentState.conditionSelected,
                                  categoryIds: [int.tryParse(dropdownCategoryKey.currentState.categorySelected)],
                                  itemSize: int.tryParse(dropdownSizeKey.currentState.sizeSelected),
                                  latitude: currentLocation.latitude,
                                  longitude: currentLocation.longitude,
                                  localGlobal: LocalGlobalHelper.getLocalGlobal());

                              var response = await ResoldRest.postProduct(
                                  customer.token, product, imageUploaderKey.currentState.imagePaths);
                              product.id = int.tryParse(response);

                              // dispatch new action to set the for-sale products
                              List<Product> forSaleProducts =
                                  await Resold.getVendorProducts(customer.vendorId, 'for-sale');

                              dispatcher(SetForSaleAction(forSaleProducts));
                              dispatcher(SetSelectedTabAction(SelectedTab.account));

                              Navigator.of(context, rootNavigator: true).pop('dialog');
                              Navigator.push(
                                  context,
                                  MaterialPageRoute(
                                      builder: (context) => ProductPage(
                                          customer: customer,
                                          currentLocation: currentLocation,
                                          product: product,
                                          dispatcher: dispatcher)));
                            } // end if form is valid
                          },
                          child: Text('Post',
                              style: new TextStyle(fontSize: 20.0, fontWeight: FontWeight.bold, color: Colors.white)),
                          color: Colors.black,
                          textColor: Colors.white,
                        ))
                  ]))
            ]));
  } // end function build
}
