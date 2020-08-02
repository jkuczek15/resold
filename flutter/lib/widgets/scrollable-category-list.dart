import 'package:flutter/material.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';

class ScrollableCategoryList extends StatefulWidget {
  @override
  ScrollableCategoryListState createState() {
    return ScrollableCategoryListState();
  }
}

class ScrollableCategoryListState extends State<ScrollableCategoryList> {

  final List<bool> categorySelected = [false, false, false, false, false, false, false, false];

  @override
  void initState() {
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    return SingleChildScrollView (
        scrollDirection: Axis.horizontal,
        child: ToggleButtons(
          children: <Widget>[
            Padding(padding: EdgeInsets.fromLTRB(20, 10, 20, 10), child: Column (
              children: [
                Icon(Icons.computer, semanticLabel: 'Electronics'),
                Text('Electronics')
              ],
            )
            ),
            Padding(padding: EdgeInsets.fromLTRB(20, 10, 20, 10), child: Column (
              children: [
                Icon(MdiIcons.tshirtCrew, semanticLabel: 'Fashion'),
                Text('Fashion')
              ],
            )
            ),
            Padding(padding: EdgeInsets.fromLTRB(20, 10, 20, 10), child: Column (
              children: [
                Icon(MdiIcons.sofa, semanticLabel: 'Home & Lawn'),
                Text('Home & Lawn')
              ],
            )
            ),
            Padding(padding: EdgeInsets.fromLTRB(20, 10, 20, 10), child: Column (
              children: [
                Icon(Icons.directions_bike, semanticLabel: 'Outdoors'),
                Text('Outdoors')
              ],
            )
            ),
            Padding(padding: EdgeInsets.fromLTRB(20, 10, 20, 10), child: Column (
              children: [
                Icon(MdiIcons.basketball, semanticLabel: 'Sporting Goods'),
                Text('Sporting Goods')
              ],
            )
            ),
            Padding(padding: EdgeInsets.fromLTRB(20, 10, 20, 10), child: Column (
              children: [
                Icon(MdiIcons.guitarAcoustic, semanticLabel: 'Music'),
                Text('Music')
              ],
            )
            ),
            Padding(padding: EdgeInsets.fromLTRB(20, 10, 20, 10), child: Column (
              children: [
                Icon(MdiIcons.cards, semanticLabel: 'Collectibles'),
                Text('Collectibles')
              ],
            )
            ),
            Padding(padding: EdgeInsets.fromLTRB(20, 10, 20, 10), child: Column (
              children: [
                Icon(MdiIcons.handHeart, semanticLabel: 'Handmade'),
                Text('Handmade')
              ],
            )
            )
          ],
          onPressed: (int index) {
            setState(() {
              for (int buttonIndex = 0; buttonIndex < categorySelected.length; buttonIndex++) {
                if (buttonIndex == index) {
                  categorySelected[buttonIndex] = true;
                } else {
                  categorySelected[buttonIndex] = false;
                }
              }
            });
          },
          isSelected: categorySelected,
        )
    );
  }
}
