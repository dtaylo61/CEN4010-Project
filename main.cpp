#include "opencv2\opencv.hpp"
#include "opencv2\nonfree\nonfree.hpp"
#include<iostream>
using namespace std;
using namespace cv;

vector<Vec3f> circles;

//Finds the location of potential coins in an image
void findCoins(Mat src_gray)
{

	GaussianBlur(src_gray, src_gray, Size(3, 3), 0, 0);
	HoughCircles(src_gray, circles, CV_HOUGH_GRADIENT, 1, src_gray.rows / 10, 90, 50, 100, 300);
	for (size_t i = 0; i < circles.size(); i++)
	{
		Point center(cvRound(circles[i][0]), cvRound(circles[i][1]));
		int radius = cvRound(circles[i][2]);
	}

}

//Find the larges of the four inputs
int findLargest(int p, int n, int d, int q)
{
	int largest = p;
	if (n>largest)largest = n;
	if (d > largest)largest = d;
	if (q > largest)largest = q;
	return largest;
}

//Finds a list of keypoints in an image
vector<KeyPoint> findKeyPoints(Mat img)
{
	vector<KeyPoint> keypoints;
	GoodFeaturesToTrackDetector mGood;
	mGood.detect(img, keypoints);
	return keypoints;
}


Mat findDescriptors(Mat key,vector<KeyPoint> pts)
{
	BriefDescriptorExtractor mExtractor;
	Mat descriptors;
	mExtractor.compute(key, pts, descriptors);
	return descriptors;
}

//find matches based on key features between two images
vector<DMatch> findMatches(Mat img1, Mat img2)
{
	//find keypoints in img1 and img2
	vector<KeyPoint>keypoints_1=findKeyPoints(img1);
	vector<KeyPoint>keypoints_2=findKeyPoints(img2);
	//find the descriptors between img1 and img2
	Mat descriptors_1 = findDescriptors(img1, keypoints_1);
	Mat descriptors_2 = findDescriptors(img2, keypoints_2);
	//Finds all matches between img1 and img2
	BFMatcher matcher(NORM_L2, true);
	vector< DMatch > good_matches;
	vector< DMatch > matches;
	if (keypoints_1.size() > 0 && keypoints_2.size() > 0)
	{
		matcher.match(descriptors_1, descriptors_2, matches);
	}

	double max_dist = 0; double min_dist = 100;
	//-- Quick calculation of max and min distances between keypoints
	for (int i = 0; i < matches.size(); i++)
	{
		double dist = matches[i].distance;
		if (dist < min_dist) min_dist = dist;
		if (dist > max_dist) max_dist = dist;
	}

	//retrives only good matches between img1 and img2 to reduce the possiblity of a false positive
	for (int i = 0; i < matches.size(); i++)
	{
		if (matches[i].distance <= max(2 * min_dist, 0.02))
		{
			good_matches.push_back(matches[i]);
		}
	}
	return good_matches;
}
int main(int argc, char**argv)
{
	//load in picture
	if (argc != 2)return -1;//checks to see if arguments are correct
	Mat src = imread(argv[1], IMREAD_COLOR); //reads in image
	if (!src.data)return-1; //checks to see if image was successfully read
	Mat src_gray;
	cvtColor(src, src_gray, COLOR_BGR2GRAY); //convert the image to gray
	//find if any circular objects are in the picture
	findCoins(src_gray);
	//If no objects found exit
	if (circles.size() <= 0)return -1;
	//load in keys
	Mat pkey = imread("penny2.jpg", IMREAD_COLOR);
	Mat dkey = imread("dime.png", IMREAD_COLOR);
	Mat nkey = imread("nickel.jpg", IMREAD_COLOR);
	Mat qkey = imread("quarter.jpg", IMREAD_COLOR);
	if (!pkey.data || !dkey.data || !nkey.data, !qkey.data)return -1; //if one of the keys fails to load exit
	int penny = 0, nickel = 0, dime = 0, quarter = 0; //coin counters
	
	//goes through each object found and checks to see which coin it could be
	for (int j = 0; j < circles.size(); j++)
	{
		int radius = circles[j][2];
		int x = circles[j][0] - radius;
		int y = circles[j][1] - radius;
		Mat sector = Mat(src, Rect(x, y, radius * 2, radius * 2)); //get just image of one coin
		GaussianBlur(sector, sector, Size(5, 5), 0, 0);
		//finds # matches for each coin
		vector<DMatch>pMatches=findMatches(pkey, sector);
		vector<DMatch>nMatches=findMatches(nkey, sector);
		vector<DMatch>dMatches=findMatches(dkey, sector);
		vector<DMatch>qMatches=findMatches(qkey, sector);
		//gets the largest matches and compaires to find which coin it is
		int countKey = findLargest(pMatches.size(), nMatches.size(), dMatches.size(), qMatches.size());
		if (countKey == pMatches.size())penny++;
		else if (countKey == nMatches.size())nickel++;
		else if (countKey == dMatches.size())dime++;
		else if (countKey == qMatches.size())quarter++;
	}
	//output count
	cout << "Pennies=" << penny << endl <<
		"Nickels=" << nickel << endl <<
		"Dimes=" << dime << endl <<
		"Quarters=" << quarter << endl;
	return 0;
}