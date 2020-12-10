import 'package:flutter/material.dart';
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
  PageController formPageViewController;
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
      this.formPageViewController,
      this.focusState,
      this.imageState});
}
