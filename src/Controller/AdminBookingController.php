<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Repository\BookingRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminBookingController extends AbstractController
{
    /**
     * @Route("/admin/bookings", name="admin_bookings_index")
     * @param Booking $booking
     * @return Response
     */
    public function index(BookingRepository $repo)
    {
        $bookings=$repo->findAll();
        return $this->render('admin/booking/index.html.twig', [
            'bookings' => $bookings,
        ]);
    }
    /**
     * Premet de modifier une reservation
     * @Route("/admin/bookings/{id}/edit", name="admin_bookings_edit")
     * @return Response
     */
    public function edit(Booking $booking,Request $request,ObjectManager $manager)
    {
        $form=$this->createForm(AdminBookingType::class,$booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {   
            $booking->setAmount(0);
            $manager->persist($booking);
            $manager->flush();

            $this->addFlash(
                'success',
                "La reservation {$booking->getId()} a bien ete modifiee !"
            );
            return $this->redirectToRoute('admin_bookings_index');
        }
        return $this->render('admin/booking/edit.html.twig', [
            'booking' => $booking,
            'form'=>$form->createView()
        ]);
    }
}
