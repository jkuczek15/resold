import 'package:flutter/material.dart';
import 'package:resold/services/resold-search.dart';
import 'package:resold/models/product.dart';
import 'package:resold/builders/product-list-builder.dart';
import 'package:resold/screens/sell.dart';
import 'package:flappy_search_bar/flappy_search_bar.dart';
import 'package:geolocator/geolocator.dart';
import 'package:liquid_pull_to_refresh/liquid_pull_to_refresh.dart';

class Home extends StatelessWidget {

  final String email;
  final String token;

  Home(String email, String token) : email = email, token = token;

  // This widget is the root of your application.
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
        title: 'Resold',
        theme: ThemeData(
            primarySwatch: const MaterialColor(0xff41b8ea, {
              50:  Color.fromRGBO(25,72,92, .1),
              100: Color.fromRGBO(25,72,92, .2),
              200: Color.fromRGBO(25,72,92, .3),
              300: Color.fromRGBO(25,72,92, .4),
              400: Color.fromRGBO(25,72,92, .5),
              500: Color.fromRGBO(25,72,92, .6),
              600: Color.fromRGBO(25,72,92, .7),
              700: Color.fromRGBO(25,72,92, .8),
              800: Color.fromRGBO(25,72,92, .9),
              900: Color.fromRGBO(25,72,92, 1)
            }),
            accentColor: Colors.white,
            primaryColor: const Color(0xff41b8ea)
        ),
        home: HomePage(email, token)
    );
  }
}

class HomePageState extends State<HomePage> {
  int selectedIndex = 0;

  Future<List<Product>> futureLocalProducts;
  Position currentLocation;

  final String email;
  final String token;

  HomePageState(this.email, this.token);

  @override
  void initState() {
    super.initState();
    Geolocator().getCurrentPosition(desiredAccuracy: LocationAccuracy.high).then((location) {
      if(this.mounted) {
        setState(() {
          currentLocation = location;
          futureLocalProducts = ResoldSearch.fetchLocalProducts(location.latitude, location.longitude);
        });
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Row (
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Align (
                alignment: Alignment.centerLeft,
                child: Image.asset('assets/images/resold-white-logo.png', width: 145, height: 145)
            ),
            Align (
                alignment: Alignment.centerRight,
                child: Icon(Icons.message, color: Colors.white),
            )
          ],
        ),
        iconTheme: IconThemeData(
          color: Colors.white, //change your color here
        ),
        backgroundColor: const Color(0xff41b8ea),
      ),
      body: Center(
        child: getContent(context),
      ),
      bottomNavigationBar: BottomNavigationBar(
        type: BottomNavigationBarType.fixed,
        items: <BottomNavigationBarItem>[
          BottomNavigationBarItem(icon: Icon(Icons.home), title: Text('Buy')),
          BottomNavigationBarItem(icon: Icon(Icons.search), title: Text('Search')),
          BottomNavigationBarItem(icon: Icon(Icons.attach_money), title: Text('Sell')),
          BottomNavigationBarItem(icon: Icon(Icons.receipt), title: Text('Orders')),
          BottomNavigationBarItem(icon: Icon(Icons.person), title: Text('Account')),
        ],
        currentIndex: selectedIndex,
        fixedColor: const Color(0xff41b8ea),
        unselectedItemColor: Colors.black,
        onTap: onItemTapped,
      ),
    );
  }

  Widget getContent(BuildContext context) {
    Widget content;
    switch(selectedIndex) {
      case 0:
          //  local buy tab
          content = LiquidPullToRefresh(
            height: 80,
            springAnimationDurationInMilliseconds: 500,
            onRefresh: () => futureLocalProducts = ResoldSearch.fetchLocalProducts(currentLocation.latitude, currentLocation.longitude),
            showChildOpacityTransition: false,
            color: const Color(0xff41b8ea),
            animSpeedFactor: 5.0,
            child: FutureBuilder<List<Product>>(
              future: futureLocalProducts,
              builder: (context, snapshot) {
                if (snapshot.hasData) {
                  return ProductListBuilder.buildProductList(context, snapshot.data, currentLocation);
                } else if (snapshot.hasError) {
                  return Text("${snapshot.error}");
                }
                // By default, show a loading spinner.
                return Center(child: CircularProgressIndicator(backgroundColor: const Color(0xff41b8ea)));
              },
            )
          );
          break;
        case 1:
          // search tab
          content = SearchBar<Product>(
            hintText: 'Search entire marketplace here...',
            searchBarPadding: EdgeInsets.symmetric(horizontal: 20),
            cancellationWidget: Icon(Icons.cancel),
            onSearch: (term) => ResoldSearch.fetchSearchProducts(term, currentLocation.latitude, currentLocation.longitude),
            loader: Center(child: CircularProgressIndicator(backgroundColor: const Color(0xff41b8ea))),
            onItemFound: (Product product, int index) {
              return ProductListBuilder.buildProductTile(context, currentLocation, product, index);
            },
            emptyWidget: Center(child: Text('Your search returned no results.')),
          );
          break;
        case 2:
          // sell tab
          content = SellPage();
          break;
        case 3:
          // orders tab
          content = Center(child: Text('Orders'));
          break;
        case 4:
          // account tab
          content = Center(child: Text('Account'));
          break;
    }
    return content;
  }

  void onItemTapped(int index) {
    setState(() {
      selectedIndex = index;
    });
  }
}

class HomePage extends StatefulWidget {

  final String email;
  final String token;

  HomePage(this.email, this.token, {Key key}) : super(key: key);

  @override
  HomePageState createState() => HomePageState(this.email, this.token);
}