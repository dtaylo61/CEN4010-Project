#include "opencv2\imgproc\imgproc.hpp"
#include"opencv2\highgui\highgui.hpp"
#include <iostream>

using namespace std;
using namespace cv;

int main(int argc, char**argv)
{
	//load in picture
	if (argc != 3)//checks to see if arguments are correct
	{
		cout << "Invalid # of arguments\n";
		return -1;
	}
	Mat src = imread(argv[1], IMREAD_COLOR); //reads in image
	if (!src.data)//checks to see if image was successfully read
	{
		cout << "Image could not be read\n";
		return-1;
	}
	Mat src_gray;
	cvtColor(src, src_gray, COLOR_BGR2GRAY); //convert the image to gray
	//find if any circular objects are in the picture
	vector<Vec3f> circles;
	GaussianBlur(src_gray, src_gray, Size(3, 3), 0, 0);
	HoughCircles(src_gray, circles, CV_HOUGH_GRADIENT, 1, src_gray.rows / 10, 90, 50, 100, 300);
	for (size_t i = 0; i < circles.size(); i++)
	{
		Point center(cvRound(circles[i][0]), cvRound(circles[i][1]));
		int radius = cvRound(circles[i][2]);
		// circle center
		circle(src, center, 3, Scalar(0, 255, 0), -1, 8, 0);
		// circle outline
		circle(src, center, radius, Scalar(0, 0, 255), 3, 8, 0);
	}
	imwrite(argv[2] , src);
	//If no objects found exit
	cout << circles.size() << endl;
	return 0;
}