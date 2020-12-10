import 'package:carousel_slider/carousel_options.dart';
import 'package:carousel_slider/carousel_slider.dart';
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter/rendering.dart';
import 'package:intl/intl.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import 'package:multi_image_picker/multi_image_picker.dart';
import 'package:resold/constants/ui-constants.dart';
import 'package:resold/enums/selected-tab.dart';
import 'package:resold/helpers/category-helper.dart';
import 'package:resold/helpers/condition-helper.dart';
import 'package:resold/helpers/size-helper.dart';
import 'package:resold/models/product.dart';
import 'package:resold/screens/product/view.dart';
import 'package:resold/services/resold-rest.dart';
import 'package:resold/state/actions/add=product.dart';
import 'package:resold/state/actions/set-selected-tab.dart';
import 'package:resold/state/actions/set-sell-state.dart';
import 'package:resold/state/screens/sell/sell-state.dart';
import 'package:resold/state/screens/sell/sell-focus-state.dart';
import 'package:resold/state/screens/sell/sell-image-state.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/widgets/image/image-uploader.dart';
import 'package:geolocator/geolocator.dart';
import 'package:resold/widgets/loading.dart';

class SellPage extends StatelessWidget {
  final String condition = '';

  // field controllers
  final TextEditingController listingTitleController;
  final TextEditingController priceController;
  final TextEditingController detailsController;

  final CustomerResponse customer;
  final Position currentLocation;
  final Function dispatcher;

  final int selectedCondition;
  final List<int> selectedCategory;
  final List<int> selectedItemSize;
  final int currentFormStep;
  final String error;

  // new
  final List<bool> conditionSelected = [false, false, false, false];
  final List<List<bool>> vehicleSelected = [
    [false, false],
    [false, false]
  ];
  final List<List<bool>> categorySelected = [
    [false, false],
    [false, false],
    [false, false],
    [false, false]
  ];
  final List<String> steps = [
    '1. Add Images',
    '2. Add Title & Details',
    '3. Select Category',
    '4. Select Vehicle',
    '5. Review and Submit'
  ];
  final Map categoriesMap = {
    'Electronics': Icons.computer,
    'Fashion': MdiIcons.tshirtCrew,
    'Home & Lawn': MdiIcons.sofa,
    'Outdoors': Icons.directions_bike,
    'Music': MdiIcons.guitarAcoustic,
    'Collectibles': MdiIcons.cards,
    'Handmade': MdiIcons.handHeart,
    'Cancel': MdiIcons.close,
  };
  final Map conditionMap = {
    'New': MdiIcons.emoticonExcitedOutline,
    'Like New': MdiIcons.emoticonHappyOutline,
    'Good': MdiIcons.emoticonNeutralOutline,
    'Used': MdiIcons.emoticonSadOutline,
    'Cancel': MdiIcons.close,
  };
  final SellFocusState focusState;
  final SellImageState imageState;
  final double categoryIconSize = 30;
  final double vehicleIconSize = 40;

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
      this.error,
      this.focusState,
      this.imageState,
      this.dispatcher});

  @override
  Widget build(BuildContext context) {
    GlobalKey<FormState> formKey = GlobalKey<FormState>();
    NumberFormat formatter = new NumberFormat("\$###,###", "en_US");
    SellState sellState = SellState(
        listingTitleController: listingTitleController,
        priceController: priceController,
        detailsController: detailsController,
        selectedCondition: selectedCondition,
        selectedItemSize: selectedItemSize,
        selectedCategory: selectedCategory,
        currentFormStep: currentFormStep,
        error: error,
        focusState: focusState,
        imageState: imageState);

    PageController formPageViewController = PageController(initialPage: currentFormStep);

    if (selectedCondition != null) {
      conditionSelected[selectedCondition] = true;
    }
    if (selectedCategory != null) {
      categorySelected[selectedCategory[0]][selectedCategory[1]] = true;
    }
    if (selectedItemSize != null) {
      vehicleSelected[selectedItemSize[0]][selectedItemSize[1]] = true;
    }

    final imageUploader =
        ImageUploader(images: imageState.images, imagePaths: imageState.imagePaths, dispatcher: dispatcher);

    final List forms = [
      Container(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          crossAxisAlignment: CrossAxisAlignment.center,
          children: <Widget>[
            imageUploader,
            Padding(
                padding: EdgeInsets.fromLTRB(50, 10, 50, 10),
                child: Column(
                  children: [
                    ButtonTheme(
                      minWidth: double.infinity,
                      child: RaisedButton(
                        shape: RoundedRectangleBorder(borderRadius: BorderRadiusDirectional.circular(8)),
                        onPressed: () async {
                          if (sellState.imageState.imagePaths.length > 0) {
                            sellState.error = '';
                            sellState.currentFormStep += 1;
                            await formPageViewController.nextPage(
                              duration: Duration(milliseconds: 300),
                              curve: Curves.ease,
                            );
                          } else {
                            sellState.error = 'Please select at least one image';
                          }
                          dispatcher(SetSellStateAction(sellState));
                        },
                        child: Text('Next',
                            style: new TextStyle(fontSize: 20.0, fontWeight: FontWeight.bold, color: Colors.white)),
                        padding: EdgeInsets.fromLTRB(50, 20, 50, 20),
                        color: Colors.black,
                        textColor: Colors.white,
                      ),
                    ),
                    SizedBox(height: 10),
                    sellState.error.isNotEmpty ? Text(sellState.error, style: TextStyle(color: Colors.red)) : SizedBox()
                  ],
                )),
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
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
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
                  onPressed: () async {
                    if (sellState.listingTitleController.text.isEmpty) {
                      sellState.error = 'Please enter a listing title';
                    } else if (sellState.priceController.text.isEmpty) {
                      sellState.error = 'Please enter a price';
                    } else if (sellState.detailsController.text.isEmpty) {
                      sellState.error = 'Please enter details';
                    } else if (sellState.selectedCondition == null) {
                      sellState.error = 'Please select a condition';
                    } else {
                      sellState.error = '';
                      sellState.currentFormStep += 1;
                      await formPageViewController.nextPage(
                        duration: Duration(milliseconds: 300),
                        curve: Curves.ease,
                      );
                    } // end if we have an error
                    sellState.focusState =
                        SellFocusState(listingTitleFocused: false, priceFocused: false, detailsFocused: false);
                    dispatcher(SetSellStateAction(sellState));
                  },
                  child: Text('Next',
                      style: new TextStyle(fontSize: 20.0, fontWeight: FontWeight.bold, color: Colors.white)),
                  padding: EdgeInsets.fromLTRB(50, 20, 50, 20),
                  color: Colors.black,
                  textColor: Colors.white,
                ),
              ),
              sellState.error.isNotEmpty ? Text(sellState.error, style: TextStyle(color: Colors.red)) : SizedBox()
            ],
          ),
        ),
      ),
      Container(
        child: Column(
          mainAxisSize: MainAxisSize.max,
          mainAxisAlignment: MainAxisAlignment.spaceEvenly,
          crossAxisAlignment: CrossAxisAlignment.center,
          children: <Widget>[
            Column(
              mainAxisSize: MainAxisSize.max,
              mainAxisAlignment: MainAxisAlignment.spaceEvenly,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(children: [
                  ToggleButtons(
                    onPressed: (int index) {
                      sellState.selectedCategory = [0, index];
                      dispatcher(SetSellStateAction(sellState));
                    },
                    renderBorder: false,
                    isSelected: categorySelected[0],
                    children: <Widget>[
                      SizedBox(
                          width: MediaQuery.of(context).size.width / 2,
                          child: FittedBox(
                            child: Column(
                              children: [
                                Icon(
                                  MdiIcons.laptop,
                                  size: categoryIconSize,
                                ),
                                SizedBox(
                                  width: MediaQuery.of(context).size.width / 2,
                                  child: Column(
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    children: [Text('Electronics')],
                                  ),
                                ),
                              ],
                            ),
                          )),
                      SizedBox(
                        width: MediaQuery.of(context).size.width / 2,
                        child: FittedBox(
                            child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          crossAxisAlignment: CrossAxisAlignment.center,
                          children: [
                            Icon(
                              MdiIcons.tshirtCrew,
                              size: categoryIconSize,
                            ),
                            SizedBox(
                              width: MediaQuery.of(context).size.width / 2,
                              child: Column(
                                children: [Text('Fashion')],
                                mainAxisAlignment: MainAxisAlignment.center,
                              ),
                            ),
                          ],
                        )),
                      ),
                    ],
                  ),
                ]),
                SizedBox(height: 30),
                Row(children: [
                  ToggleButtons(
                    onPressed: (int index) {
                      sellState.selectedCategory = [1, index];
                      dispatcher(SetSellStateAction(sellState));
                    },
                    renderBorder: false,
                    isSelected: categorySelected[1],
                    children: <Widget>[
                      SizedBox(
                          width: MediaQuery.of(context).size.width / 2,
                          child: FittedBox(
                            child: Column(
                              children: [
                                Icon(
                                  MdiIcons.sofa,
                                  size: categoryIconSize,
                                ),
                                SizedBox(
                                  width: MediaQuery.of(context).size.width / 2,
                                  child: Column(
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    children: [Text('Home & Lawn')],
                                  ),
                                ),
                              ],
                            ),
                          )),
                      SizedBox(
                        width: MediaQuery.of(context).size.width / 2,
                        child: FittedBox(
                            child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          crossAxisAlignment: CrossAxisAlignment.center,
                          children: [
                            Icon(
                              MdiIcons.bike,
                              size: categoryIconSize,
                            ),
                            SizedBox(
                              width: MediaQuery.of(context).size.width / 2,
                              child: Column(
                                children: [Text('Outdoors')],
                                mainAxisAlignment: MainAxisAlignment.center,
                              ),
                            ),
                          ],
                        )),
                      ),
                    ],
                  ),
                ]),
                SizedBox(height: 30),
                Row(children: [
                  ToggleButtons(
                    onPressed: (int index) {
                      sellState.selectedCategory = [2, index];
                      dispatcher(SetSellStateAction(sellState));
                    },
                    renderBorder: false,
                    isSelected: categorySelected[2],
                    children: <Widget>[
                      SizedBox(
                          width: MediaQuery.of(context).size.width / 2,
                          child: FittedBox(
                            child: Column(
                              children: [
                                Icon(
                                  MdiIcons.basketball,
                                  size: categoryIconSize,
                                ),
                                SizedBox(
                                  width: MediaQuery.of(context).size.width / 2,
                                  child: Column(
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    children: [Text('Sporting Goods')],
                                  ),
                                ),
                              ],
                            ),
                          )),
                      SizedBox(
                        width: MediaQuery.of(context).size.width / 2,
                        child: FittedBox(
                            child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          crossAxisAlignment: CrossAxisAlignment.center,
                          children: [
                            Icon(
                              MdiIcons.guitarAcoustic,
                              size: categoryIconSize,
                            ),
                            SizedBox(
                              width: MediaQuery.of(context).size.width / 2,
                              child: Column(
                                children: [Text('Music')],
                                mainAxisAlignment: MainAxisAlignment.center,
                              ),
                            ),
                          ],
                        )),
                      ),
                    ],
                  ),
                ]),
                SizedBox(height: 30),
                Row(children: [
                  ToggleButtons(
                    onPressed: (int index) {
                      sellState.selectedCategory = [3, index];
                      dispatcher(SetSellStateAction(sellState));
                    },
                    renderBorder: false,
                    isSelected: categorySelected[3],
                    children: <Widget>[
                      SizedBox(
                          width: MediaQuery.of(context).size.width / 2,
                          child: FittedBox(
                            child: Column(
                              children: [
                                Icon(
                                  MdiIcons.cards,
                                  size: categoryIconSize,
                                ),
                                SizedBox(
                                  width: MediaQuery.of(context).size.width / 2,
                                  child: Column(
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    children: [Text('Collectibles')],
                                  ),
                                ),
                              ],
                            ),
                          )),
                      SizedBox(
                        width: MediaQuery.of(context).size.width / 2,
                        child: FittedBox(
                            child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          crossAxisAlignment: CrossAxisAlignment.center,
                          children: [
                            Icon(
                              MdiIcons.handHeart,
                              size: categoryIconSize,
                            ),
                            SizedBox(
                              width: MediaQuery.of(context).size.width / 2,
                              child: Column(
                                children: [Text('Handmade')],
                                mainAxisAlignment: MainAxisAlignment.center,
                              ),
                            ),
                          ],
                        )),
                      ),
                    ],
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
                  onPressed: () async {
                    if (sellState.selectedCategory == null) {
                      sellState.error = 'Please select a category';
                    } else {
                      sellState.error = '';
                      sellState.currentFormStep += 1;
                      await formPageViewController.nextPage(
                        duration: Duration(milliseconds: 300),
                        curve: Curves.ease,
                      );
                    } // end if error
                    dispatcher(SetSellStateAction(sellState));
                  },
                  child: Text('Next',
                      style: new TextStyle(fontSize: 20.0, fontWeight: FontWeight.bold, color: Colors.white)),
                  padding: EdgeInsets.fromLTRB(50, 20, 50, 20),
                  color: Colors.black,
                  textColor: Colors.white,
                ),
              ),
            ),
            sellState.error.isNotEmpty ? Text(sellState.error, style: TextStyle(color: Colors.red)) : SizedBox()
          ],
        ),
      ),
      Container(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.spaceEvenly,
          crossAxisAlignment: CrossAxisAlignment.center,
          children: <Widget>[
            Column(
              mainAxisAlignment: MainAxisAlignment.spaceEvenly,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(children: [
                  ToggleButtons(
                      onPressed: (int index) {
                        sellState.selectedItemSize = [0, index];
                        dispatcher(SetSellStateAction(sellState));
                      },
                      renderBorder: false,
                      isSelected: vehicleSelected[0],
                      children: <Widget>[
                        SizedBox(
                            width: MediaQuery.of(context).size.width / 2,
                            child: FittedBox(
                              child: Column(
                                children: [
                                  Icon(MdiIcons.carSide, size: vehicleIconSize),
                                  SizedBox(
                                    width: MediaQuery.of(context).size.width / 2,
                                    child: Column(
                                      mainAxisAlignment: MainAxisAlignment.center,
                                      children: [Text('Car')],
                                    ),
                                  ),
                                ],
                              ),
                            )),
                        SizedBox(
                          width: MediaQuery.of(context).size.width / 2,
                          child: FittedBox(
                              child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            crossAxisAlignment: CrossAxisAlignment.center,
                            children: [
                              Icon(MdiIcons.carPickup, size: vehicleIconSize),
                              SizedBox(
                                width: MediaQuery.of(context).size.width / 2,
                                child: Column(
                                  children: [Text('Pickup Truck')],
                                  mainAxisAlignment: MainAxisAlignment.center,
                                ),
                              ),
                            ],
                          )),
                        ),
                      ]),
                ]),
                SizedBox(height: 30),
                Row(children: [
                  ToggleButtons(
                      onPressed: (int index) {
                        sellState.selectedItemSize = [1, index];
                        dispatcher(SetSellStateAction(sellState));
                      },
                      renderBorder: false,
                      isSelected: vehicleSelected[1],
                      children: <Widget>[
                        SizedBox(
                            width: MediaQuery.of(context).size.width / 2,
                            child: FittedBox(
                              child: Column(
                                children: [
                                  Icon(MdiIcons.vanUtility, size: vehicleIconSize),
                                  SizedBox(
                                    width: MediaQuery.of(context).size.width / 2,
                                    child: Column(
                                      mainAxisAlignment: MainAxisAlignment.center,
                                      children: [Text('Delivery Van')],
                                    ),
                                  ),
                                ],
                              ),
                            )),
                        SizedBox(
                          width: MediaQuery.of(context).size.width / 2,
                          child: FittedBox(
                              child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            crossAxisAlignment: CrossAxisAlignment.center,
                            children: [
                              Icon(MdiIcons.truck, size: vehicleIconSize),
                              SizedBox(
                                width: MediaQuery.of(context).size.width / 2,
                                child: Column(
                                  children: [Text('Moving Truck')],
                                  mainAxisAlignment: MainAxisAlignment.center,
                                ),
                              ),
                            ],
                          )),
                        ),
                      ]),
                ]),
              ],
            ),
            Padding(
              padding: EdgeInsets.fromLTRB(50, 10, 50, 10),
              child: ButtonTheme(
                minWidth: double.infinity,
                child: RaisedButton(
                  shape: RoundedRectangleBorder(borderRadius: BorderRadiusDirectional.circular(8)),
                  onPressed: () async {
                    if (sellState.selectedItemSize == null) {
                      sellState.error = 'Please select a vehicle required';
                    } else {
                      sellState.error = '';
                      sellState.currentFormStep += 1;
                      await formPageViewController.nextPage(
                        duration: Duration(milliseconds: 300),
                        curve: Curves.ease,
                      );
                    } // end if error
                    dispatcher(SetSellStateAction(sellState));
                  },
                  child: Text('Next',
                      style: new TextStyle(fontSize: 20.0, fontWeight: FontWeight.bold, color: Colors.white)),
                  padding: EdgeInsets.fromLTRB(50, 20, 50, 20),
                  color: Colors.black,
                  textColor: Colors.white,
                ),
              ),
            ),
            sellState.error.isNotEmpty ? Text(sellState.error, style: TextStyle(color: Colors.red)) : SizedBox()
          ],
        ),
      ),
      Container(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          crossAxisAlignment: CrossAxisAlignment.center,
          children: <Widget>[
            CarouselSlider(
                options: CarouselOptions(height: 350.0),
                items: sellState.imageState.images.map((image) {
                  return Builder(builder: (BuildContext context) {
                    return Container(
                      width: MediaQuery.of(context).size.width,
                      margin: EdgeInsets.symmetric(horizontal: 10.0, vertical: 10.0),
                      child: AssetThumb(asset: image, width: 200, height: 200),
                    );
                  });
                }).toList()),
            Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Column(
                      mainAxisAlignment: MainAxisAlignment.start,
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Container(
                          padding: new EdgeInsets.fromLTRB(10, 0, 0, 0),
                          width: 250,
                          child: new Text(
                            sellState.listingTitleController.text,
                            overflow: TextOverflow.fade,
                            style: new TextStyle(
                              fontSize: 14.0,
                              fontFamily: 'Roboto',
                              fontWeight: FontWeight.normal,
                            ),
                          ),
                        ),
                        Container(
                          padding: new EdgeInsets.fromLTRB(10, 0, 0, 0),
                          width: 250,
                          child: new Text(
                            sellState.detailsController.text ?? "",
                            overflow: TextOverflow.fade,
                            style: new TextStyle(
                              fontSize: 14.0,
                              fontFamily: 'Roboto',
                              fontWeight: FontWeight.normal,
                            ),
                          ),
                        ),
                      ]),
                  Column(
                      mainAxisAlignment: MainAxisAlignment.end,
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Container(
                            width: 70,
                            child: Padding(
                                padding: new EdgeInsets.fromLTRB(0, 0, 10, 0),
                                child: Align(
                                    alignment: Alignment.centerRight,
                                    child: priceController.text.isNotEmpty
                                        ? Text(formatter.format(double.parse(sellState.priceController.text)),
                                            style: new TextStyle(
                                              fontSize: 14.0,
                                              fontFamily: 'Roboto',
                                              fontWeight: FontWeight.bold,
                                            ))
                                        : SizedBox()))),
                      ])
                ]),
            Padding(
              padding: EdgeInsets.fromLTRB(50, 10, 50, 10),
              child: ButtonTheme(
                minWidth: double.infinity,
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

                      Product product = Product(
                          name: sellState.listingTitleController.text,
                          price: sellState.priceController.text,
                          description: sellState.detailsController.text,
                          condition: ConditionHelper.getConditionIdByIndex(sellState.selectedCondition),
                          categoryIds: [int.tryParse(CategoryHelper.getCategoryIdByMatrix(sellState.selectedCategory))],
                          itemSize: int.tryParse(SizeHelper.getSizeIdByMatrix(sellState.selectedItemSize)),
                          latitude: currentLocation.latitude,
                          longitude: currentLocation.longitude,
                          localGlobal: '231,232');

                      String response =
                          await ResoldRest.postProduct(customer.token, product, sellState.imageState.imagePaths);

                      // fetch the product again
                      product = await ResoldRest.getProduct(customer.token, int.tryParse(response));

                      // dispatch new action to set the for-sale products
                      dispatcher(AddProductAction(product: product));
                      dispatcher(SetSelectedTabAction(SelectedTab.account));
                      dispatcher(SetSellStateAction(SellState.initialState()));
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

    // store app bar to get the height
    AppBar appBar = AppBar(
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
                                sellState.error = '';
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
    );
    return Form(
      key: formKey,
      child: PageView.builder(
        controller: formPageViewController,
        physics: NeverScrollableScrollPhysics(),
        itemBuilder: (BuildContext context, int index) {
          return Scaffold(
            resizeToAvoidBottomPadding: false,
            appBar: appBar,
            body: SingleChildScrollView(
                child: ConstrainedBox(
                    constraints: BoxConstraints(
                      maxHeight: MediaQuery.of(context).size.height - (appBar.preferredSize.height * 4),
                    ),
                    child: forms[sellState.currentFormStep])),
          );
        },
      ),
    );
  } // end function build
}
