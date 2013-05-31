<?php

namespace YZ\SupervisorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * SupervisorController
 */
class SupervisorController extends Controller
{
    /**
     * indexAction
     */
    public function indexAction()
    {
        $supervisorManager = $this->get('supervisor.manager');

        return $this->render('YZSupervisorBundle:Supervisor:list.html.twig', array(
            'supervisors' => $supervisorManager->getSupervisors(),
        ));
    }

    /**
     * startStopProcessAction
     *
     * @param string $start 1 to start, 0 to stop it
     * @param string $key   The key to retrieve a Supervisor object
     * @param string $name  The name of a process
     * @param string $group The group of a process
     *
     * @return Symfony\Component\HttpFoundation\Response represents an HTTP response.
     */
    public function startStopProcessAction($start, $key, $name, $group)
    {
        $supervisorManager = $this->get('supervisor.manager');
        $supervisor = $supervisorManager->getSupervisorByKey($key);
        $process = $supervisor->getProcessByNameAndGroup($name, $group);

        if (!$supervisor) {
            throw new \Exception('Supervisor not found');
        }

        if ($start == "1") {
            if ($process->startProcess() != true) {
                $this->get('session')->getFlashBag()->add('error', 'Erreur lors du lancement du processus.');
            }
        } elseif ($start == "0") {
            if ($process->stopProcess() != true) {
                $this->get('session')->getFlashBag()->add('error', 'Erreur lors de l\'arret du processus.');
            }
        }

        return $this->redirect($this->generateUrl('supervisor'));
    }

    /**
     * startStopAllProcessesAction
     *
     * @param string $start 1 to start, 0 to stop it
     * @param string $key   The key to retrieve a Supervisor object
     */
    public function startStopAllProcessesAction($start, $key)
    {
        $supervisorManager = $this->get('supervisor.manager');
        $supervisor = $supervisorManager->getSupervisorByKey($key);

        if (!$supervisor) {
            throw new \Exception('Supervisor not found');
        }

        if ($start == "1") {
            if ($supervisor->startAllProcesses() != true) {
                $this->get('session')->getFlashBag()->add('error', 'Erreur lors du lancement de tous processus.');
            }
        } elseif ($start == "0") {
            if ($supervisor->stopAllProcesses() != true) {
                $this->get('session')->getFlashBag()->add('error', 'Erreur lors de l\'arret de tous les processus.');
            }
        }

        return $this->redirect($this->generateUrl('supervisor'));
    }

    /**
     * showSupervisorLogAction
     *
     * @param string $key The key to retrieve a Supervisor object
     */
    public function showSupervisorLogAction($key)
    {
        $supervisorManager = $this->get('supervisor.manager');
        $supervisor = $supervisorManager->getSupervisorByKey($key);

        if (!$supervisor) {
            throw new \Exception('Supervisor not found');
        }

        $logs = $supervisor->readLog(0, 0);

        return $this->render('YZSupervisorBundle:Supervisor:showLog.html.twig', array(
            'log' => $logs,
        ));
    }

    /**
     * clearSupervisorLogAction
     *
     * @param string $key The key to retrieve a Supervisor object
     */
    public function clearSupervisorLogAction($key)
    {
        $supervisorManager = $this->get('supervisor.manager');
        $supervisor = $supervisorManager->getSupervisorByKey($key);

        if (!$supervisor) {
            throw new \Exception('Supervisor not found');
        }

        if ($supervisor->clearLog() != true) {
            $this->get('session')->getFlashBag()->add('error', 'Erreur lors de la suppression des logs.');
        }

        return $this->redirect($this->generateUrl('supervisor'));
    }

    /**
     * showProcessLogAction
     *
     * @param string $key   The key to retrieve a Supervisor object
     * @param string $name  The name of a process
     * @param string $group The group of a process
     */
    public function showProcessLogAction($key, $name, $group)
    {
        $supervisorManager = $this->get('supervisor.manager');
        $supervisor = $supervisorManager->getSupervisorByKey($key);
        $process = $supervisor->getProcessByNameAndGroup($name, $group);

        if (!$supervisor) {
            throw new \Exception('Supervisor not found');
        }

        $result = $process->tailProcessStdoutLog(0, 1);
        $stdout = $process->tailProcessStdoutLog(0, $result[1]);

        return $this->render('YZSupervisorBundle:Supervisor:showLog.html.twig', array(
            'log' => $stdout[0],
        ));
    }

    /**
     * showProcessLogErrAction
     *
     * @param string $key   The key to retrieve a Supervisor object
     * @param string $name  The name of a process
     * @param string $group The group of a process
     */
    public function showProcessLogErrAction($key, $name, $group)
    {
        $supervisorManager = $this->get('supervisor.manager');
        $supervisor = $supervisorManager->getSupervisorByKey($key);
        $process = $supervisor->getProcessByNameAndGroup($name, $group);

        if (!$supervisor) {
            throw new \Exception('Supervisor not found');
        }

        $result = $process->tailProcessStderrLog(0, 1);
        $stderr = $process->tailProcessStderrLog(0, $result[1]);

        return $this->render('YZSupervisorBundle:Supervisor:showLog.html.twig', array(
            'log' => $stderr[0],
        ));
    }

    /**
     * clearProcessLogAction
     *
     * @param string $key   The key to retrieve a Supervisor object
     * @param string $name  The name of a process
     * @param string $group The group of a process
     */
    public function clearProcessLogAction($key, $name, $group)
    {
        $supervisorManager = $this->get('supervisor.manager');
        $supervisor = $supervisorManager->getSupervisorByKey($key);
        $process = $supervisor->getProcessByNameAndGroup($name, $group);

        if (!$supervisor) {
            throw new \Exception('Supervisor not found');
        }

        if ($process->clearProcessLogs() != true) {
            $this->get('session')->getFlashBag()->add('error', 'Erreur lors de la suppression des logs.');
        }

        return $this->redirect($this->generateUrl('supervisor'));
    }

    /**
     * showProcessInfoAction
     *
     * @param string $key   The key to retrieve a Supervisor object
     * @param string $name  The name of a process
     * @param string $group The group of a process
     *
     * @return Symfony\Component\HttpFoundation\Response represents an HTTP response.
     */
    public function showProcessInfoAction($key, $name, $group)
    {
        $supervisorManager = $this->get('supervisor.manager');
        $supervisor = $supervisorManager->getSupervisorByKey($key);
        $process = $supervisor->getProcessByNameAndGroup($name, $group);

        if (!$supervisor) {
            throw new \Exception('Supervisor not found');
        }

        $infos = $process->getProcessInfo();

        return $this->render('YZSupervisorBundle:Supervisor:showInformations.html.twig', array(
            'informations' => $infos,
        ));
    }
}
