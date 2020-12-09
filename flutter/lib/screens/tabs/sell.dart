import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter/rendering.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import 'package:resold/constants/ui-constants.dart';
import 'package:resold/state/actions/set-sell-state.dart';
import 'package:resold/state/screens/sell-state.dart';
import 'package:resold/state/screens/sell/sell-focus-state.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/widgets/image/image-uploader.dart';
import 'package:resold/widgets/dropdown/dropdown-category-list.dart';
import 'package:geolocator/geolocator.dart';
import 'package:resold/widgets/dropdown/dropdown-size-list.dart';
import 'package:resold/widgets/dropdown/dropdown-condition-list.dart';

class SellPage extends StatelessWidget {
  final String condition = '';

  // widget keys
  final imageUploaderKey = new GlobalKey<ImageUploaderState>();
  final dropdownCategoryKey = new GlobalKey<DropdownCategoryListState>();
  final dropdownSizeKey = new GlobalKey<DropdownSizeListState>();
  final dropdownConditionKey = new GlobalKey<DropdownConditionListState>();

  // field controllers
  final TextEditingController listingTitleController;
  final TextEditingController priceController;
  final TextEditingController detailsController;
  var formKey = GlobalKey<FormState>();

  final CustomerResponse customer;
  final Position currentLocation;
  final Function dispatcher;

  final int selectedCondition;
  final int selectedCategory;
  final int selectedItemSize;
  final int currentFormStep;

  // new
  final List<bool> conditionSelected = [false, false, false, false];
  final List<bool> vehicleSelected = [false, false, false, false];
  List<String> steps = ['1. Add Images', '2. Add Title and Details', '3. Select Category', '4. Review and Submit'];
  Map categoriesMap = {
    'Electronics': Icons.computer,
    'Fashion': MdiIcons.tshirtCrew,
    'Home & Lawn': MdiIcons.sofa,
    'Outdoors': Icons.directions_bike,
    'Music': MdiIcons.guitarAcoustic,
    'Collectibles': MdiIcons.cards,
    'Handmade': MdiIcons.handHeart,
    'Cancel': MdiIcons.close,
  };
  Map conditionMap = {
    'New': MdiIcons.emoticonExcitedOutline,
    'Like New': MdiIcons.emoticonHappyOutline,
    'Good': MdiIcons.emoticonNeutralOutline,
    'Used': MdiIcons.emoticonSadOutline,
    'Cancel': MdiIcons.close,
  };
  final List<IconData> _icons = [
    MdiIcons.carSide,
    MdiIcons.carPickup,
    MdiIcons.vanUtility,
    MdiIcons.truck,
  ];
  List _forms;
  SellState sellState;
  PageController formPageViewController;
  SellFocusState focusState;
  List<IconData> _selectedIcons = [];

  SellPage(
      {this.customer,
      this.currentLocation,
      this.listingTitleController,
      this.priceController,
      this.detailsController,
      this.selectedCondition,
      this.selectedCategory,
      this.selectedItemSize,
      this.currentFormStep,
      this.focusState,
      this.dispatcher});

  @override
  GlobalKey<FormState> createState() {
    return this.formKey = GlobalKey<FormState>();
  }

  @override
  Widget build(BuildContext context) {
    sellState = SellState(
        listingTitleController: listingTitleController,
        priceController: priceController,
        detailsController: detailsController,
        selectedCondition: selectedCondition,
        selectedItemSize: selectedItemSize,
        selectedCategory: selectedCategory,
        currentFormStep: currentFormStep,
        focusState: focusState);

    formPageViewController = PageController(initialPage: currentFormStep);

    if (selectedCondition != null) {
      conditionSelected[selectedCondition] = true;
    }
    var counter = 0;

    final imageUploader = ImageUploader(key: imageUploaderKey);
    final dropdownCategoryList = DropdownCategoryList(key: dropdownCategoryKey);
    final dropdownSizeList = DropdownSizeList(key: dropdownSizeKey);
    final dropdownConditionList = DropdownConditionList(key: dropdownConditionKey);
    _forms = [
      Container(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          crossAxisAlignment: CrossAxisAlignment.center,
          children: <Widget>[
            Text('Upload Images'),
            //imageUploader,
            Padding(
              padding: EdgeInsets.fromLTRB(50, 10, 50, 10),
              child: ButtonTheme(
                minWidth: double.infinity,
                child: RaisedButton(
                  shape: RoundedRectangleBorder(borderRadius: BorderRadiusDirectional.circular(8)),
                  onPressed: () => {_nextFormStep()},
                  child: Text('Next',
                      style: new TextStyle(fontSize: 20.0, fontWeight: FontWeight.bold, color: Colors.white)),
                  padding: EdgeInsets.fromLTRB(50, 20, 50, 20),
                  color: Colors.black,
                  textColor: Colors.white,
                ),
              ),
            ),
          ],
        ),
      ),
      Container(
        child: Padding(
          padding: EdgeInsets.fromLTRB(25, 0, 25, 0),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.spaceEvenly,
            crossAxisAlignment: CrossAxisAlignment.center,
            children: <Widget>[
              Focus(
                child: TextFormField(
                  style: TextStyle(color: ResoldBlue),
                  autofocus: sellState.focusState.listingTitleFocused,
                  onTap: () {
                    sellState.focusState =
                        SellFocusState(listingTitleFocused: true, priceFocused: false, detailsFocused: false);
                    dispatcher(SetSellStateAction(sellState));
                  },
                  controller: listingTitleController,
                  decoration: InputDecoration(
                      labelText: 'Listing Title',
                      labelStyle: TextStyle(
                          color: sellState.focusState.listingTitleFocused || listingTitleController.text.isNotEmpty
                              ? ResoldBlue
                              : Colors.black),
                      enabledBorder: UnderlineInputBorder(
                          borderSide: BorderSide(
                              color: listingTitleController.text.isNotEmpty ? ResoldBlue : Colors.black, width: 1.5)),
                      focusedBorder: UnderlineInputBorder(borderSide: BorderSide(color: ResoldBlue, width: 1.5))),
                  validator: (value) {
                    if (value.isEmpty) {
                      return "Please enter your listing's title.";
                    }
                    return null;
                  },
                ),
                onFocusChange: (bool hasFocus) {
                  if (!hasFocus) {
                    dispatcher(SetSellStateAction(sellState));
                  }
                },
              ),
              TextFormField(
                autofocus: sellState.focusState.priceFocused,
                style: TextStyle(color: ResoldBlue),
                onTap: () {
                  sellState.focusState =
                      SellFocusState(listingTitleFocused: false, priceFocused: true, detailsFocused: false);
                  dispatcher(SetSellStateAction(sellState));
                },
                controller: priceController,
                keyboardType: TextInputType.number,
                decoration: InputDecoration(
                    labelText: 'Price (\$)',
                    labelStyle: TextStyle(
                        color: sellState.focusState.priceFocused || priceController.text.isNotEmpty
                            ? ResoldBlue
                            : Colors.black),
                    enabledBorder: UnderlineInputBorder(
                        borderSide:
                            BorderSide(color: priceController.text.isNotEmpty ? ResoldBlue : Colors.black, width: 1.5)),
                    focusedBorder: UnderlineInputBorder(borderSide: BorderSide(color: ResoldBlue, width: 1.5))),
                validator: (value) {
                  if (value.isEmpty) {
                    return 'Please enter a price.';
                  }
                  return null;
                },
              ),
              TextFormField(
                autofocus: sellState.focusState.detailsFocused,
                style: TextStyle(color: ResoldBlue),
                controller: detailsController,
                onTap: () {
                  sellState.focusState =
                      SellFocusState(listingTitleFocused: false, priceFocused: false, detailsFocused: true);
                  dispatcher(SetSellStateAction(sellState));
                },
                maxLines: null,
                minLines: null,
                keyboardType: TextInputType.multiline,
                decoration: InputDecoration(
                  labelText: 'Details',
                  labelStyle: TextStyle(
                      color: sellState.focusState.detailsFocused || detailsController.text.isNotEmpty
                          ? ResoldBlue
                          : Colors.black),
                  enabledBorder: UnderlineInputBorder(
                      borderSide:
                          BorderSide(color: detailsController.text.isNotEmpty ? ResoldBlue : Colors.black, width: 1.5)),
                  focusedBorder: UnderlineInputBorder(borderSide: BorderSide(color: ResoldBlue, width: 1.5)),
                ),
                validator: (value) {
                  if (value.isEmpty) {
                    return "Please your listing's details";
                  }
                  return null;
                },
              ),
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Padding(
                    padding: EdgeInsets.fromLTRB(0, 0, 0, 20),
                    child: Text(
                      'Select Condition',
                      style: TextStyle(color: Colors.black, fontSize: 16),
                    ),
                  ),
                  ToggleButtons(
                      onPressed: (int index) {
                        sellState.selectedCondition = index;
                        sellState.focusState =
                            SellFocusState(listingTitleFocused: false, priceFocused: false, detailsFocused: false);
                        dispatcher(SetSellStateAction(sellState));
                      },
                      renderBorder: false,
                      isSelected: conditionSelected,
                      children: <Widget>[
                        SizedBox(
                            width: MediaQuery.of(context).size.width * 0.215,
                            child: FittedBox(
                              child: Column(
                                children: [
                                  Icon(
                                    MdiIcons.emoticonExcitedOutline,
                                  ),
                                  Text('New')
                                ],
                              ),
                            )),
                        SizedBox(
                          width: MediaQuery.of(context).size.width * 0.215,
                          child: FittedBox(
                              child: Column(
                            children: [Icon(MdiIcons.emoticonHappyOutline), Text('Like New')],
                          )),
                        ),
                        SizedBox(
                          width: MediaQuery.of(context).size.width * 0.215,
                          child: FittedBox(
                              child: Column(
                            children: [Icon(MdiIcons.emoticonNeutralOutline), Text('Good')],
                          )),
                        ),
                        SizedBox(
                          width: MediaQuery.of(context).size.width * 0.215,
                          child: FittedBox(
                              child: Column(
                            children: [Icon(MdiIcons.emoticonSadOutline), Text('Used')],
                          )),
                        ),
                      ]),
                ],
              ),
              ButtonTheme(
                minWidth: double.infinity,
                child: RaisedButton(
                  shape: RoundedRectangleBorder(borderRadius: BorderRadiusDirectional.circular(8)),
                  onPressed: () => {_nextFormStep()},
                  child: Text('Next',
                      style: new TextStyle(fontSize: 20.0, fontWeight: FontWeight.bold, color: Colors.white)),
                  padding: EdgeInsets.fromLTRB(50, 20, 50, 20),
                  color: Colors.black,
                  textColor: Colors.white,
                ),
              ),
            ],
          ),
        ),
      ),
      Container(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.spaceEvenly,
          crossAxisAlignment: CrossAxisAlignment.center,
          children: <Widget>[
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Padding(
                  padding: EdgeInsets.fromLTRB(0, 0, 0, 20),
                  child: Text(
                    'Select Vehicle Required',
                    style: TextStyle(color: Colors.black, fontSize: 16),
                  ),
                ),
                /* SizedBox(
                  width: MediaQuery.of(context).size.width * 0.45,
                  child: FittedBox(
                    child: Column(
                      children: [Icon(MdiIcons.carSide), Text('Car')],
                    ),
                  ),
                ), */
                ToggleButtons(
                    onPressed: (int index) {
                      sellState.selectedCondition = index;
                      sellState.focusState =
                          SellFocusState(listingTitleFocused: false, priceFocused: false, detailsFocused: false);
                      dispatcher(SetSellStateAction(sellState));
                    },
                    renderBorder: false,
                    isSelected: conditionSelected,
                    children: <Widget>[
                      SizedBox(
                          width: MediaQuery.of(context).size.width * 0.215,
                          child: FittedBox(
                            child: Column(
                              children: [
                                Icon(
                                  MdiIcons.carSide,
                                ),
                                SizedBox(
                                  width: MediaQuery.of(context).size.width * 0.15,
                                  height: MediaQuery.of(context).size.height * 0.05,
                                  child: Column(
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    children: [Text('Car')],
                                  ),
                                ),
                              ],
                            ),
                          )),
                      SizedBox(
                        width: MediaQuery.of(context).size.width * 0.215,
                        child: FittedBox(
                            child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          crossAxisAlignment: CrossAxisAlignment.center,
                          children: [
                            Icon(MdiIcons.carPickup),
                            SizedBox(
                              width: MediaQuery.of(context).size.width * 0.15,
                              height: MediaQuery.of(context).size.height * 0.05,
                              child: Column(
                                children: [Text('Pickup Truck')],
                                mainAxisAlignment: MainAxisAlignment.center,
                              ),
                            ),
                          ],
                        )),
                      ),
                      SizedBox(
                        width: MediaQuery.of(context).size.width * 0.215,
                        child: FittedBox(
                            child: Column(
                          children: [Icon(MdiIcons.vanUtility), Text('Delivery Van')],
                        )),
                      ),
                      SizedBox(
                        width: MediaQuery.of(context).size.width * 0.215,
                        child: FittedBox(
                            child: Column(
                          children: [Icon(MdiIcons.truck), Text('Moving Truck')],
                        )),
                      ),
                    ]),
              ],
            ),
            Padding(
              padding: EdgeInsets.fromLTRB(50, 10, 50, 10),
              child: ButtonTheme(
                minWidth: double.infinity,
                child: RaisedButton(
                  shape: RoundedRectangleBorder(borderRadius: BorderRadiusDirectional.circular(8)),
                  onPressed: () => {_nextFormStep()},
                  child: Text('Next',
                      style: new TextStyle(fontSize: 20.0, fontWeight: FontWeight.bold, color: Colors.white)),
                  padding: EdgeInsets.fromLTRB(50, 20, 50, 20),
                  color: Colors.black,
                  textColor: Colors.white,
                ),
              ),
            ),
          ],
        ),
      ),
      Container(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          crossAxisAlignment: CrossAxisAlignment.center,
          children: <Widget>[
            Text('Review'),
            Padding(
              padding: EdgeInsets.fromLTRB(50, 10, 50, 10),
              child: ButtonTheme(
                minWidth: double.infinity,
                child: RaisedButton(
                  shape: RoundedRectangleBorder(borderRadius: BorderRadiusDirectional.circular(8)),
                  onPressed: () => {},
                  child: Text('Post',
                      style: new TextStyle(fontSize: 20.0, fontWeight: FontWeight.bold, color: Colors.white)),
                  padding: EdgeInsets.fromLTRB(50, 20, 50, 20),
                  color: Colors.black,
                  textColor: Colors.white,
                ),
              ),
            ),
          ],
        ),
      )
    ];
    return Form(
        key: formKey,
        child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            crossAxisAlignment: CrossAxisAlignment.center,
            children: [
              Expanded(
                child: PageView.builder(
                  controller: formPageViewController,
                  physics: NeverScrollableScrollPhysics(),
                  itemBuilder: (BuildContext context, int index) {
                    return Scaffold(
                      appBar: AppBar(
                        title: Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Align(
                                alignment: Alignment.center,
                                child: Row(mainAxisAlignment: MainAxisAlignment.center, children: [
                                  SizedBox(
                                      child: sellState.currentFormStep != 0
                                          ? BackButton(
                                              onPressed: () {
                                                formPageViewController
                                                    .previousPage(
                                                  duration: Duration(milliseconds: 300),
                                                  curve: Curves.ease,
                                                )
                                                    .then((value) {
                                                  sellState.currentFormStep -= 1;
                                                  dispatcher(SetSellStateAction(sellState));
                                                });
                                              },
                                            )
                                          : SizedBox(),
                                      width: 35),
                                  Text(steps[sellState.currentFormStep], style: new TextStyle(color: Colors.white))
                                ]))
                          ],
                        ),
                        iconTheme: IconThemeData(
                          color: Colors.white, // change your color here
                        ),
                        backgroundColor: ResoldBlue,
                        actions: <Widget>[],
                      ),
                      body: _forms[sellState.currentFormStep],
                    );
                  },
                ),
              )
            ]));
  }

  void _nextFormStep() async {
    sellState.currentFormStep += 1;
    await formPageViewController.nextPage(
      duration: Duration(milliseconds: 300),
      curve: Curves.ease,
    );
    dispatcher(SetSellStateAction(sellState));
  }
}

/*return Padding(
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
            ]));*/
//  } // end function build

//}
