import 'dart:collection';

import 'package:flutter/material.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import 'package:resold/constants/ui-constants.dart';
import 'package:resold/services/resold.dart';

class ScrollableFilterList extends StatefulWidget {
  final ScrollableFilterListState state = new ScrollableFilterListState();

  ScrollableFilterList({Key key}) : super(key: key);

  @override
  ScrollableFilterListState createState() => state;
}

class ScrollableFilterListState extends State<ScrollableFilterList> {

  //List categories = 
  Map categoriesMap = {
                  'Electronics': Icons.computer,
                  'Fashion': MdiIcons.tshirtCrew, 
                  'Home & Lawn': MdiIcons.sofa, 
                  'Outdoors': Icons.directions_bike,
                  'Music': MdiIcons.guitarAcoustic,
                  'Collectibles' : MdiIcons.cards,
                  'Handmade' : MdiIcons.handHeart,
                  'Cancel': MdiIcons.close, 
                  };
  Map conditionMap = {
                  'New': MdiIcons.emoticonExcitedOutline,
                  'Like New': MdiIcons.emoticonHappyOutline, 
                  'Good': MdiIcons.emoticonNeutralOutline, 
                  'Used': MdiIcons.emoticonSadOutline,
                  'Cancel': MdiIcons.close, 
                  };
  List basesortlist = ['Newest', '(\$) Low to High', '(\$) High to Low'];

  TextStyle categoryTextStyle = new TextStyle();


  Column dropdownCategory = Column(children: [
            Icon(MdiIcons.filterMenu),
            Text('Category'),
            ],);
  Column dropdownCondition = Column(children: [
            Icon(MdiIcons.sparkles),
            Text('Condition'),
            ],);

  @override
  void initState() {
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    return Row (
      mainAxisAlignment: MainAxisAlignment.spaceEvenly,
      children: <Widget> [
        PopupMenuButton <PopupMenuItem> (
          child: dropdownCategory,
          onSelected: selectedCategory,
          itemBuilder: (BuildContext context) {
            var categorylist = List<PopupMenuItem>();
            categoriesMap.forEach((t,i) => 
            categorylist.add(
              PopupMenuItem(
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.start,
                  children: [
                    Icon(i),
                    SizedBox(width: 10),
                    Text(t),
                  ],
                ),
                value: PopupMenuItem(value: t, child:Text(t)),
              ),
            ));
            return categorylist
            .map((PopupMenuItem choice) {
              return PopupMenuItem<PopupMenuItem>(
                value: choice,
                child: choice,
                );
              }).toList();
            }
        ),
        PopupMenuButton <PopupMenuItem> (
          child: Column(children: [
            Icon(MdiIcons.mapMarkerRadius),
            Text('Distance'),
            ],),
          //onSelected: selectedCategory,
          itemBuilder: (BuildContext context) {
            var distancelist = List<PopupMenuItem>();
            distancelist.add(
              PopupMenuItem(
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.start,
                  children: [
                    Text('HI'),
                  ],
                ),
              ),
            );
            return distancelist
            .map((PopupMenuItem choice) {
              return PopupMenuItem<PopupMenuItem>(
                value: choice,
                child: choice,
                );
              }).toList();
            }
        ),
        PopupMenuButton <PopupMenuItem> (
          child: dropdownCondition,
          onSelected: selectedCondition,
          itemBuilder: (BuildContext context) {
            var conditionList = List<PopupMenuItem>();
            conditionMap.forEach((t,i) => 
            conditionList.add(
              PopupMenuItem(
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.start,
                  children: [
                    Icon(i),
                    SizedBox(width: 10),
                    Text(t),
                  ],
                ),
                value: PopupMenuItem(value: t, child:Text(t)),
              ),
            ));
            return conditionList
            .map((PopupMenuItem choice) {
              return PopupMenuItem<PopupMenuItem>(
                value: choice,
                child: choice,
                );
              }).toList();
            }
        ),
                PopupMenuButton <PopupMenuItem> (
          child: Column(children: [
            Icon(MdiIcons.sort),
            Text('Sort'),
            ],),
          //onSelected: selectedCategory,
          itemBuilder: (BuildContext context) {
            var sortlist = List.generate(basesortlist.length, (index) => 
              PopupMenuItem(
                child: Container(
                  //height: double.infinity,
                  //width: double.infinity,
                  //height: double.infinity,
                  color: ResoldBlue,
                  child: Row(
                  mainAxisAlignment: MainAxisAlignment.start,
                  children: [
                    Text(basesortlist[index]),
                  ],
                ),),
              ),);
            return sortlist
            .map((PopupMenuItem choice) {
              return PopupMenuItem<PopupMenuItem>(
                value: choice,
                child: choice,
                );
              }).toList();
            }
        ),
      ]
    );

  }
  void selectedCategory(PopupMenuItem choice) async {
    setState(() {
      if(choice.value != 'Cancel') {
      dropdownCategory = Column(children: [
            Icon(categoriesMap[choice.value]),
            Text(choice.value),
            ],);
      }
      else{
        dropdownCategory = Column(children: [
            Icon(MdiIcons.filterMenu),
            Text('Category'),
            ],);
      }
    });
  }
    void selectedCondition(PopupMenuItem choice) async {
    setState(() {
      if(choice.value != 'Cancel') {
      dropdownCondition = Column(children: [
            Icon(conditionMap[choice.value]),
            Text(choice.value),
            ],);
      }
      else{
        dropdownCondition = Column(children: [
            Icon(MdiIcons.sparkles),
            Text('Condition'),
            ],);
      }
    });
  }
}