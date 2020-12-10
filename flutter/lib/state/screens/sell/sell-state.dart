import 'package:flutter/material.dart';
import 'package:multi_image_picker/multi_image_picker.dart';
import 'package:resold/state/screens/sell/sell-focus-state.dart';
import 'package:resold/state/screens/sell/sell-image-state.dart';

class SellState {
  TextEditingController listingTitleController;
  TextEditingController priceController;
  TextEditingController detailsController;
  List<int> selectedCategory;
  int selectedCondition;
  List<int> selectedItemSize;
  int currentFormStep;
  SellFocusState focusState;
  SellImageState imageState;

  SellState(
      {this.listingTitleController,
      this.priceController,
      this.detailsController,
      this.selectedCategory,
      this.selectedCondition,
      this.selectedItemSize,
      this.currentFormStep,
      this.focusState,
      this.imageState});

  factory SellState.initialState() {
    return SellState(
        listingTitleController: TextEditingController(),
        priceController: TextEditingController(),
        detailsController: TextEditingController(),
        focusState: SellFocusState(listingTitleFocused: false, priceFocused: false, detailsFocused: false),
        imageState: SellImageState(images: new List<Asset>(), imagePaths: new List<String>()),
        currentFormStep: 0);
  } // end function initialState
}
