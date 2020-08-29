import 'package:flutter/material.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';

class ScrollableSizeList extends StatefulWidget {

  final ScrollableSizeListState state = new ScrollableSizeListState();

  ScrollableSizeList({Key key}) : super(key: key);

  @override
  ScrollableSizeListState createState() => state;
}

class ScrollableSizeListState extends State<ScrollableSizeList> {

  final List<bool> sizeSelected = [false, false, false, false];

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
                Icon(MdiIcons.sizeS, semanticLabel: 'Small'),
                Text('Small')
              ],
            )
            ),
            Padding(padding: EdgeInsets.fromLTRB(20, 10, 20, 10), child: Column (
              children: [
                Icon(MdiIcons.sizeM, semanticLabel: 'Medium'),
                Text('Medium')
              ],
            )
            ),
            Padding(padding: EdgeInsets.fromLTRB(20, 10, 20, 10), child: Column (
              children: [
                Icon(MdiIcons.sizeL, semanticLabel: 'Large'),
                Text('Large')
              ],
            )
            ),
            Padding(padding: EdgeInsets.fromLTRB(20, 10, 20, 10), child: Column (
              children: [
                Icon(MdiIcons.sizeXl, semanticLabel: 'Extra Large'),
                Text('Extra Large')
              ],
            )
            )
          ],
          onPressed: (int index) {
            setState(() {
              for (int buttonIndex = 0; buttonIndex < sizeSelected.length; buttonIndex++) {
                if (buttonIndex == index) {
                  sizeSelected[buttonIndex] = true;
                } else {
                  sizeSelected[buttonIndex] = false;
                }
              }
            });
          },
          isSelected: sizeSelected
        )
    );
  }
}
